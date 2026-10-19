<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\B2CItinerary;
use App\Services\LeadFollowupScheduleService;
use Illuminate\Http\Request;

class FollowupController extends Controller
{
    public function index(Request $request, LeadFollowupScheduleService $schedule)
    {
        $now = now();
        $baseQuery = B2CItinerary::with(['destination', 'user'])
            ->where(function ($query) {
                $query->whereNull('followup_status')
                    ->orWhereNotIn('followup_status', LeadFollowupScheduleService::TERMINAL_STATUSES);
            })
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn('status', LeadFollowupScheduleService::TERMINAL_STATUSES);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('followup_paused_until')
                    ->orWhere('followup_paused_until', '<=', $now);
            });

        if ($request->filled('assigned_to')) {
            $baseQuery->where('user_id', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($query) use ($search) {
                $query->where('client_name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $dueCount = (clone $baseQuery)->whereNotNull('next_followup_at')->where('next_followup_at', '<=', $now)->count();
        $upcomingCount = (clone $baseQuery)->where('next_followup_at', '>', $now)->count();
        $unscheduledCount = (clone $baseQuery)->whereNull('next_followup_at')->count();

        $filter = $request->get('filter', 'due');
        if ($filter === 'upcoming') {
            $baseQuery->where('next_followup_at', '>', $now);
        } elseif ($filter === 'all') {
            // Leave the active queue unfiltered.
        } else {
            $baseQuery->where(function ($query) use ($now) {
                $query->whereNull('next_followup_at')->orWhere('next_followup_at', '<=', $now);
            });
        }

        $leads = $baseQuery->orderByRaw('CASE WHEN next_followup_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('next_followup_at')
            ->paginate(20)
            ->withQueryString();
        $admins = \App\Models\Admin::orderBy('name')->get(['id', 'name']);

        return view('admin.followups.index', compact('leads', 'admins', 'filter', 'dueCount', 'upcomingCount', 'unscheduledCount'));
    }

    public function complete(Request $request, $id, LeadFollowupScheduleService $schedule)
    {
        $request->validate([
            'followup_status' => 'nullable|in:leads,quoted,interested,waiting',
        ]);

        $lead = B2CItinerary::findOrFail($id);
        if ($schedule->isTerminal($lead)) {
            return redirect()->back()->with('error', 'This lead is already closed.');
        }

        $lead->followup_status = $request->followup_status ?: $lead->followup_status ?: 'leads';
        $lead->followed_up_at = now();
        $schedule->scheduleNext($lead);
        $lead->save();

        return redirect()->back()->with('success', 'Follow-up completed. The next reminder is scheduled for 12:30 PM India time in two days.');
    }

    public function snooze(Request $request, $id, LeadFollowupScheduleService $schedule)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $lead = B2CItinerary::findOrFail($id);
        if ($schedule->isTerminal($lead)) {
            return redirect()->back()->with('error', 'This lead is already closed.');
        }

        $schedule->scheduleForDate($lead, $request->date);
        $lead->save();

        return redirect()->back()->with('success', 'Reminder moved to 12:30 PM India time on the selected date.');
    }

    public function close(Request $request, $id, LeadFollowupScheduleService $schedule)
    {
        $request->validate([
            'status' => 'required|in:confirmed,cancelled',
        ]);

        $lead = B2CItinerary::findOrFail($id);
        $lead->followup_status = $request->status;
        $lead->followed_up_at = now();
        $schedule->stop($lead);
        $lead->save();

        return redirect()->back()->with('success', 'Lead closed and future follow-up reminders stopped.');
    }
}
