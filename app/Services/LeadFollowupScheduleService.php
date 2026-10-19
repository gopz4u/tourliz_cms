<?php

namespace App\Services;

use App\Models\B2CItinerary;
use Carbon\Carbon;

class LeadFollowupScheduleService
{
    public const TIMEZONE = 'Asia/Kolkata';
    public const TERMINAL_STATUSES = ['confirmed', 'cancelled', 'converted', 'dead', 'not_interested', 'travelled'];

    public function isTerminal(B2CItinerary $lead): bool
    {
        return in_array($lead->followup_status, self::TERMINAL_STATUSES, true)
            || in_array($lead->status, self::TERMINAL_STATUSES, true);
    }

    public function initialDueAt(?Carbon $from = null): Carbon
    {
        $from = ($from ?: now())->copy()->timezone(self::TIMEZONE);
        $minimumDueAt = $from->copy()->addDay();
        $dueAt = $minimumDueAt->copy()->startOfDay()->setTime(12, 30, 0);

        // Never remind a brand-new lead before a full 24 hours has passed.
        if ($dueAt->lt($minimumDueAt)) {
            $dueAt->addDay();
        }

        return $dueAt->utc();
    }

    public function nextDueAt(?Carbon $from = null): Carbon
    {
        $from = ($from ?: now())->copy()->timezone(self::TIMEZONE);

        return $from->copy()->addDays(2)->startOfDay()->setTime(12, 30, 0)->utc();
    }

    public function dueAtForDate($date): Carbon
    {
        return Carbon::parse($date, self::TIMEZONE)->startOfDay()->setTime(12, 30, 0)->utc();
    }

    public function scheduleInitial(B2CItinerary $lead): void
    {
        if ($this->isTerminal($lead)) {
            $this->stop($lead);
            return;
        }

        $this->setDueAt($lead, $this->initialDueAt($lead->created_at));
    }

    public function scheduleNext(B2CItinerary $lead): void
    {
        if ($this->isTerminal($lead)) {
            $this->stop($lead);
            return;
        }

        $this->setDueAt($lead, $this->nextDueAt());
    }

    public function scheduleForDate(B2CItinerary $lead, $date): void
    {
        if ($this->isTerminal($lead)) {
            $this->stop($lead);
            return;
        }

        $this->setDueAt($lead, $this->dueAtForDate($date));
    }

    public function ensureScheduled(B2CItinerary $lead): void
    {
        if ($this->isTerminal($lead) || $lead->next_followup_at) {
            return;
        }

        if ($lead->next_followup_date) {
            $this->setDueAt($lead, $this->dueAtForDate($lead->next_followup_date));
            return;
        }

        $localNow = now()->timezone(self::TIMEZONE);
        $date = $localNow->copy()->startOfDay();
        if ($localNow->gte($date->copy()->setTime(12, 30, 0))) {
            $date->addDay();
        }
        $this->setDueAt($lead, $this->dueAtForDate($date));
    }

    public function stop(B2CItinerary $lead): void
    {
        $lead->next_followup_at = null;
        $lead->next_followup_date = null;
        $lead->followup_paused_until = null;
    }

    private function setDueAt(B2CItinerary $lead, Carbon $dueAt): void
    {
        $lead->next_followup_at = $dueAt;
        $lead->next_followup_date = $dueAt->copy()->timezone(self::TIMEZONE)->toDateString();
        $lead->followup_paused_until = null;
    }
}
