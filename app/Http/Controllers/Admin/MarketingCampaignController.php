<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\B2CItinerary;
use App\Models\Booking;
use App\Models\FixedItinerary;
use App\Models\MarketingCampaign;
use App\Models\MarketingCampaignRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingCampaignController extends Controller
{
    public function __construct()
    {
        MarketingCampaign::ensureSchema();
    }

    public function index()
    {
        $campaigns = MarketingCampaign::with('fixedItinerary')->withCount([
            'recipients',
            'recipients as sent_count' => fn ($query) => $query->where('status', 'sent'),
        ])->latest()->paginate(15);

        return view('admin.marketing-campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $fixedItineraries = FixedItinerary::where('status', 'active')->orderBy('title')->get();
        $contacts = $this->contacts();

        return view('admin.marketing-campaigns.create', compact('fixedItineraries', 'contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fixed_itinerary_id' => 'required|exists:fixed_itineraries,id',
            'message_mode' => 'required|in:summary,full',
            'intro_message' => 'nullable|string|max:1000',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|string',
        ]);

        $contacts = $this->contacts()->keyBy('key');
        $selected = collect($validated['recipients'])->unique()->map(fn ($key) => $contacts->get($key))->filter();
        abort_if($selected->isEmpty(), 422, 'Select at least one valid WhatsApp contact.');

        $campaign = DB::transaction(function () use ($validated, $selected) {
            $campaign = MarketingCampaign::create([
                'user_id' => auth()->id(),
                'fixed_itinerary_id' => $validated['fixed_itinerary_id'],
                'name' => $validated['name'],
                'message_mode' => $validated['message_mode'],
                'intro_message' => $validated['intro_message'] ?? null,
                'status' => 'active',
            ]);

            foreach ($selected as $contact) {
                $campaign->recipients()->create([
                    'source_type' => $contact['source_type'],
                    'source_id' => $contact['source_id'],
                    'name' => $contact['name'],
                    'phone' => $contact['phone'],
                    'status' => 'pending',
                ]);
            }

            return $campaign;
        });

        return redirect()->route('admin.marketing-campaigns.show', $campaign)->with('success', 'Campaign created. Attach the poster manually after WhatsApp opens.');
    }

    public function show(MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->load(['fixedItinerary.destination', 'recipients']);
        $messageTemplate = $this->buildMessage($marketingCampaign);

        return view('admin.marketing-campaigns.show', compact('marketingCampaign', 'messageTemplate'));
    }

    public function updateRecipient(Request $request, MarketingCampaign $marketingCampaign, MarketingCampaignRecipient $recipient)
    {
        abort_unless($recipient->marketing_campaign_id === $marketingCampaign->id, 404);
        $validated = $request->validate(['status' => 'required|in:pending,sent,skipped,failed']);
        $recipient->update([
            'status' => $validated['status'],
            'sent_at' => $validated['status'] === 'sent' ? now() : null,
        ]);

        return back()->with('success', "{$recipient->name} marked as {$validated['status']}.");
    }

    private function contacts()
    {
        $b2c = B2CItinerary::query()->whereNotNull('phone')->where('phone', '!=', '')->latest('updated_at')->get(['id', 'client_name', 'phone'])
            ->map(fn ($item) => ['key' => 'b2c:' . $item->id, 'source_type' => 'b2c', 'source_id' => $item->id, 'name' => $item->client_name ?: 'Guest', 'phone' => $item->phone]);
        $bookings = Booking::query()->whereNotNull('phone')->where('phone', '!=', '')->latest('updated_at')->get()
            ->map(fn ($item) => ['key' => 'booking:' . $item->id, 'source_type' => 'booking', 'source_id' => $item->id, 'name' => $item->customer_name ?: ($item->name ?: 'Guest'), 'phone' => $item->customer_phone ?: $item->phone]);

        return $b2c->concat($bookings)->filter(fn ($contact) => trim((string) $contact['phone']) !== '')->unique(fn ($contact) => preg_replace('/\D+/', '', $contact['phone']))->values();
    }

    private function buildMessage(MarketingCampaign $campaign): string
    {
        $itinerary = $campaign->fixedItinerary;
        $lines = ['Hello {name},'];
        if ($campaign->intro_message) $lines[] = $campaign->intro_message;
        $lines[] = '';
        $lines[] = '*' . $itinerary->title . '*';
        $lines[] = 'Destination: ' . ($itinerary->destination->name ?? 'Tour Package');
        $lines[] = 'Duration: ' . $itinerary->duration;
        $lines[] = 'Package Price: *' . ($itinerary->currency ?: 'MYR') . ' ' . number_format((float) $itinerary->fixed_price, 2) . '*';

        if (!empty($itinerary->highlights)) {
            $lines[] = '';
            $lines[] = '*Highlights*';
            foreach ($itinerary->highlights as $item) $lines[] = '• ' . $item;
        }

        if ($campaign->message_mode === 'full') {
            $lines[] = '';
            $lines[] = '*Full Itinerary*';
            foreach ($itinerary->days ?: [] as $index => $day) {
                $lines[] = '';
                $lines[] = '*Day ' . ($day['day_number'] ?? ($index + 1)) . ': ' . ($day['title'] ?? 'Tour Program') . '*';
                if (!empty($day['description'])) $lines[] = $day['description'];
                foreach ($day['items'] ?? [] as $item) $lines[] = '• ' . ($item['title'] ?? ucfirst($item['item_type'] ?? 'Service'));
            }
        }

        foreach ([['Inclusions', $itinerary->inclusions], ['Exclusions', $itinerary->exclusions]] as [$heading, $items]) {
            if (empty($items)) continue;
            $lines[] = '';
            $lines[] = '*' . $heading . '*';
            foreach ($items as $item) $lines[] = '• ' . $item;
        }

        $lines[] = '';
        $lines[] = 'Reply to this message for booking and availability.';
        return implode("\n", $lines);
    }
}
