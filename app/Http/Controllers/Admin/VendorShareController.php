<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItineraryExpense;
use App\Models\CustomItinerary;
use App\Models\B2CItinerary;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class VendorShareController extends Controller
{
    /**
     * Generate WhatsApp message for a vendor based on an expense entry.
     */
    public function whatsapp($id)
    {
        $expense = ItineraryExpense::with(['supplier'])->findOrFail($id);
        $supplier = $expense->supplier;

        if (!$supplier) {
            return response()->json(['error' => 'No supplier linked to this expense'], 400);
        }

        $itinerary = $this->getItinerary($expense->itinerary_id, $expense->itinerary_type);



        $text = $this->generateMessage($expense, $supplier, $itinerary);

        return response()->json([
            'text' => $text,
            'phone' => $supplier->phone
        ]);
    }



    /**
     * Generate PDF for a vendor based on an expense entry.
     */
    public function pdf($id)
    {
        try {
            $expense = ItineraryExpense::with(['supplier'])->findOrFail($id);
            $supplier = $expense->supplier;

            if (!$supplier) {
                abort(400, 'No supplier linked to this expense');
            }

            $itinerary = $this->getItinerary($expense->itinerary_id, $expense->itinerary_type);

            $data = [
                'expense' => $expense,
                'supplier' => $supplier,
                'itinerary' => $itinerary,
                'trip_schedule' => $expense->category === 'Transport' ? $this->extractTransportSchedule($itinerary) : null,
                'hotel_details' => $this->extractHotelDetails($itinerary, $supplier),
                'hotel_nights' => $expense->category === 'Hotel' ? $this->countHotelNights($itinerary, $supplier) : null,
                'vehicle_types' => $expense->category === 'Transport' ? $this->extractVehicleTypes($itinerary) : null,
                'total_pax' => ($itinerary->adults + $itinerary->children_2_6 + $itinerary->children_6_11),
                'generated_at' => now()->format('d M Y H:i'),
            ];

            $pdf = Pdf::loadView('admin.expenses.vendor_pdf', $data);

            $filename = 'Voucher_' . Str::slug($supplier->name) . '_' . now()->format('d_M_Y') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("PDF Gen Error: " . $e->getMessage());
            abort(500, "Error generating PDF: " . $e->getMessage());
        }
    }

    private function getItinerary($id, $type)
    {
        if ($type === 'b2b') {
            return CustomItinerary::with(['agency', 'destination'])->findOrFail($id);
        } else {
            return B2CItinerary::with(['destination'])->findOrFail($id);
        }
    }

    private function generateMessage($expense, $supplier, $itinerary)
    {
        $clientName = $itinerary->client_name ?? 'Valued Client';
        $pax = $itinerary->adults . ' Adults';
        if ($itinerary->children_2_6 > 0)
            $pax .= ', ' . $itinerary->children_2_6 . ' Child (2-6y)';
        if ($itinerary->children_6_11 > 0)
            $pax .= ', ' . $itinerary->children_6_11 . ' Child (6-11y)';

        $hotelDetails = $this->extractHotelDetails($itinerary, $supplier);

        $text = "*BOOKING VOUCHER / REQUEST*\n";
        $text .= "---------------------------\n";
        $text .= "*Vendor:* " . $supplier->name . "\n";
        $text .= "*Category:* " . $expense->category . "\n";
        $text .= "*Ref:* " . ($itinerary->quote_id ?? 'ITIN-' . $itinerary->id) . "\n\n";

        $text .= "*Client:* " . $clientName . "\n";
        if (!empty($itinerary->phone)) $text .= "*Phone:* " . $itinerary->phone . "\n";
        if (!empty($itinerary->email)) $text .= "*Email:* " . $itinerary->email . "\n";
        $text .= "*Pax:* " . $pax . "\n";
        $durationLine = $itinerary->duration_days . " Days / " . max(0, $itinerary->duration_days - 1) . " Nights";
        $text .= "*Duration:* " . $durationLine . "\n";
        $text .= "*Travel Date:* " . ($itinerary->start_date ? $itinerary->start_date->format('d M Y') : 'TBA') . "\n";
        $text .= "*Room Type:* " . ($hotelDetails ?? '') . "\n";
        $text .= "*Destination:* " . ($itinerary->destination->name ?? 'N/A') . "\n\n";

        if ($expense->category === 'Hotel') {
            $text .= "*Hotel Booking Request:*\n";
            $text .= "Stay Date: " . $expense->expense_date->format('d M Y') . "\n";
            $nights = $this->countHotelNights($itinerary, $supplier);
            if ($nights > 0) {
                $text .= "Booking Duration: " . $nights . " Night(s)\n";
            }
            $text .= "Hotel/Room Details: " . ($expense->description ?? 'Accommodation') . "\n";
        } elseif ($expense->category === 'Transport') {
            $text .= "*Transport Booking Request:*\n";
            $text .= "Service Date: " . $expense->expense_date->format('d M Y') . "\n";
            $text .= "Vehicle/Service: " . ($expense->description ?? 'Transport') . "\n";

            // Total Pax
            $totalPax = $itinerary->adults + $itinerary->children_2_6 + $itinerary->children_6_11;
            $text .= "Total Pax: " . $totalPax . " Pax\n";

            // Extract Vehicles
            $vehicleTypes = $this->extractVehicleTypes($itinerary);
            if (!empty($vehicleTypes)) {
                $text .= "Vehicle Type(s): " . $vehicleTypes . "\n\n";
            } else {
                $text .= "\n";
            }

            // Add Schedule
            $schedule = $this->extractTransportSchedule($itinerary);
            if (!empty($schedule)) {
                $text .= "*Trip Itinerary / Schedule:*\n";
                $text .= $schedule . "\n";
            }
        } elseif ($expense->category === 'Activity' || $expense->category === 'Ticket') {
            $text .= "*Ticket / Activity Booking:*\n";
            $text .= "Date: " . $expense->expense_date->format('d M Y') . "\n";
            $text .= "Activity: " . ($expense->description ?? 'Entrance Ticket') . "\n";
            $text .= "*Pax Breakdown:*\n";
            $text .= "• Adults: " . $itinerary->adults . "\n";
            if ($itinerary->children_2_6 > 0) {
                $text .= "• Children (2-6y): " . $itinerary->children_2_6 . "\n";
            }
            if ($itinerary->children_6_11 > 0) {
                $text .= "• Children (6-11y): " . $itinerary->children_6_11 . "\n";
            }
            $text .= "\n";
        } else {
            $text .= "*Service Request:*\n";
            $text .= "Date: " . $expense->expense_date->format('d M Y') . "\n";
            $text .= "Details: " . ($expense->description ?? 'N/A') . "\n";
        }

        // Calculate System Cost if Expense Amount is 0
        $displayAmount = $expense->amount;
        if ($displayAmount <= 0) {
            $displayAmount = $this->calculateSystemCost($itinerary, $supplier, $expense->category);
        }

        if ($displayAmount > 0) {
            $text .= "\n*Total Payout:* " . $expense->currency . " " . number_format($displayAmount, 2) . "\n";
        }
        $text .= "---------------------------\n";
        $text .= "Please confirm the booking.\n";
        $text .= "Regards,\n";
        $text .= "Tourliz Operations";

        return $text;
    }

    private function extractVehicleTypes($itinerary)
    {
        $days = $itinerary->itinerary ?? [];
        $types = [];
        foreach ($days as $day) {
            $transports = $day['transport'] ?? [];
            foreach ($transports as $t) {
                if (!empty($t['mode']))
                    $types[] = $t['mode'];
            }
        }
        return implode(', ', array_unique($types));
    }

    private function extractTransportSchedule($itinerary)
    {
        $days = $itinerary->itinerary ?? [];
        if (empty($days))
            return '';

        $lines = [];
        $startDate = $itinerary->start_date ? \Carbon\Carbon::parse($itinerary->start_date) : null;

        foreach ($days as $index => $day) {
            $dayNum = $day['day'] ?? ($index + 1);

            // Calculate Date
            $dateStr = '';
            if ($startDate) {
                // $dayNum is 1-based usually
                $currentDate = $startDate->copy()->addDays($dayNum - 1);
                $dateStr = " (" . $currentDate->format('d M') . ")";
            }

            $daySubject = $day['title'] ?? ('Day ' . $dayNum);
            $dayTitle = "Day " . $dayNum . ": " . $daySubject . $dateStr;
            $dayContent = [];

            // 1. Transports
            $transports = $day['transport'] ?? [];
            if (!empty($transports)) {
                foreach ($transports as $t) {
                    $mode = $t['mode'] ?? 'Vehicle';
                    $from = $t['from'] ?? '?';
                    $to = $t['to'] ?? '?';
                    // $time = $t['time'] ?? '';
                    $dayContent[] = "   - Trip: $mode ($from -> $to)";
                }
            }

            // 2. Activities & Spots
            $sightseeing = [];
            if (!empty($day['activities']) && is_array($day['activities'])) {
                foreach ($day['activities'] as $act) {
                    if (!empty($act['name']))
                        $sightseeing[] = $act['name'];
                }
            }
            if (!empty($day['spots']) && is_array($day['spots'])) {
                foreach ($day['spots'] as $spot) {
                    if (!empty($spot['name']))
                        $sightseeing[] = $spot['name'];
                }
            }

            if (empty($sightseeing) && !empty($day['program'])) {
                $dayContent[] = "   - Plan: " . Str::limit($day['program'], 60);
            }
            if (!empty($sightseeing)) {
                $dayContent[] = "   - Visit: " . implode(', ', $sightseeing);
            }

            // 3. Hotel Details
            $hotels = [];
            if (!empty($day['hotels']) && is_array($day['hotels'])) {
                foreach ($day['hotels'] as $h) {
                    if (!empty($h['name'])) {
                        $hotels[] = $h['name'] . (!empty($h['type']) ? " (" . $h['type'] . ")" : "");
                    }
                }
            } elseif (!empty($day['hotel']['name'])) {
                $hotels[] = $day['hotel']['name'] . (!empty($day['hotel']['type']) ? " (" . $day['hotel']['type'] . ")" : "");
            }
            if (!empty($hotels)) {
                $dayContent[] = "   - Hotel: " . implode(', ', $hotels);
            }

            // 4. Meal Details
            $meals = [];
            if (!empty($day['meals'])) {
                $m = $day['meals'];
                // Handle both older format (array of names) and newer format (object with breakfast/lunch/dinner)
                if (is_array($m)) {
                    if (isset($m['breakfast']) && strtolower($m['breakfast']) !== 'not included') $meals[] = "Breakfast";
                    if (isset($m['lunch']) && strtolower($m['lunch']) !== 'not included') $meals[] = "Lunch";
                    if (isset($m['dinner']) && strtolower($m['dinner']) !== 'not included') $meals[] = "Dinner";
                    
                    // Fallback for simple indexed array of names
                    if (empty($meals) && isset($m[0])) {
                        foreach($m as $name) $meals[] = is_array($name) ? ($name['name'] ?? 'Meal') : $name;
                    }
                }
            }
            if (!empty($meals)) {
                $dayContent[] = "   - Meals: " . implode(', ', $meals);
            }

            if (!empty($dayContent)) {
                $lines[] = "• $dayTitle";
                foreach ($dayContent as $c) {
                    $lines[] = $c;
                }
                $lines[] = "";
            }
        }

        return implode("\n", $lines);
    }

    private function extractHotelDetails($itinerary, $supplier)
    {
        $days = $itinerary->itinerary ?? [];
        $details = [];
        $totalPax = ($itinerary->adults + $itinerary->children_2_6 + $itinerary->children_6_11);

        foreach ($days as $day) {
            // Check plural 'hotels'
            if (!empty($day['hotels']) && is_array($day['hotels'])) {
                foreach ($day['hotels'] as $h) {
                    if ($this->isVendorMatch($h, $supplier)) {
                        $type = $h['type'] ?? 'Standard';
                        $qty = !empty($h['quantity']) ? $h['quantity'] : 1;
                        $details[] = "$qty x $type ($totalPax Pax)";
                    }
                }
            }
            // Check singular 'hotel'
            elseif (isset($day['hotel']) && $this->isVendorMatch($day['hotel'], $supplier)) {
                $type = $day['hotel']['type'] ?? 'Standard';
                $qty = !empty($day['hotel']['quantity']) ? $day['hotel']['quantity'] : 1;
                $details[] = "$qty x $type ($totalPax Pax)";
            }
        }

        return !empty($details) ? implode(', ', array_unique($details)) : null;
    }

    private function countHotelNights($itinerary, $supplier)
    {
        $days = $itinerary->itinerary ?? [];
        $nights = 0;
        foreach ($days as $day) {
            $foundMatchInDay = false;
            if (!empty($day['hotels']) && is_array($day['hotels'])) {
                foreach ($day['hotels'] as $h) {
                    if ($this->isVendorMatch($h, $supplier)) {
                        $foundMatchInDay = true;
                        break;
                    }
                }
            } elseif (isset($day['hotel']) && $this->isVendorMatch($day['hotel'], $supplier)) {
                $foundMatchInDay = true;
            }

            if ($foundMatchInDay) {
                $nights++;
            }
        }
        return $nights;
    }

    private function calculateSystemCost($itinerary, $supplier, $category)
    {
        $total = 0;
        $days = $itinerary->itinerary ?? [];

        foreach ($days as $day) {
            // Hotels
            if ($category === 'Hotel' && !empty($day['hotels'])) {
                foreach ($day['hotels'] as $h) {
                    if ($this->isVendorMatch($h, $supplier)) {
                        $price = floatval($h['price_per_night'] ?? 0);
                        $addon = floatval($h['add_on_price'] ?? 0);
                        $qty = floatval($h['quantity'] ?? 1);
                        $total += ($price + $addon) * $qty;
                    }
                }
            }

            // Transport
            if ($category === 'Transport' && !empty($day['transport'])) {
                foreach ($day['transport'] as $t) {
                    if ($this->isVendorMatch($t, $supplier)) {
                        $total += floatval($t['price'] ?? 0);
                    }
                }
            }

            // Transport (Key Difference: sometimes keys are 'transports')
            if ($category === 'Transport' && !empty($day['transports'])) {
                foreach ($day['transports'] as $t) {
                    if ($this->isVendorMatch($t, $supplier)) {
                        $total += floatval($t['price'] ?? 0);
                    }
                }
            }

            // Activities / Tickets
            if (($category === 'Activity' || $category === 'Ticket')) {
                // Activities
                if (!empty($day['activities'])) {
                    foreach ($day['activities'] as $a) {
                        if ($this->isVendorMatch($a, $supplier)) {
                            $total += $this->calculateActivityCost($a);
                        }
                    }
                }
                // Places (Tickets)
                if (!empty($day['places'])) {
                    foreach ($day['places'] as $p) {
                        if ($this->isVendorMatch($p, $supplier)) {
                            $total += $this->calculateActivityCost($p);
                        }
                    }
                }
                // Spots (usually hourly)
                if (!empty($day['spots'])) {
                    foreach ($day['spots'] as $s) {
                        if ($this->isVendorMatch($s, $supplier)) {
                            $total += (floatval($s['hours'] ?? 0) * floatval($s['price_per_hour'] ?? 0));
                        }
                    }
                }
            }

            // Meals
            if ($category === 'Meal' && !empty($day['meals'])) {
                foreach ($day['meals'] as $m) {
                    if ($this->isVendorMatch($m, $supplier)) {
                        $price = floatval($m['price'] ?? 0);
                        $qty = floatval($m['quantity'] ?? 1);
                        $total += $price * $qty;
                    }
                }
            }
        }

        return $total;
    }

    private function isVendorMatch($item, $supplier)
    {
        if (isset($item['supplier_id']) && $item['supplier_id'] == $supplier->id) {
            return true;
        }
        // Fallback to name match
        $name = $item['name'] ?? $item['attraction_name'] ?? '';
        if (empty($name))
            return false;

        // Check if supplier name is in item name OR item name is in supplier name
        if (stripos($name, $supplier->name) !== false || stripos($supplier->name, $name) !== false) {
            return true;
        }
        return false;
    }

    private function calculateActivityCost($item)
    {
        // Logic for Entry Ticket cost structure in JSON
        // entry_ticket: { adult_price, adult_qty, child..., etc }
        $et = $item['entry_ticket'] ?? [];
        if (empty($et))
            return floatval($item['price'] ?? 0); // fallback

        $cost = 0;
        $cost += (floatval($et['adult_price'] ?? 0) * floatval($et['adult_qty'] ?? 0));
        $cost += (floatval($et['child_2_6_price'] ?? 0) * floatval($et['child_2_6_qty'] ?? 0));
        $cost += (floatval($et['child_6_11_price'] ?? 0) * floatval($et['child_6_11_qty'] ?? 0));
        return $cost;
    }
}
