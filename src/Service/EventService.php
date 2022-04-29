<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;

class EventService
{
    public function getStatsForEvent(Event $event): array
    {
        $guestCounter = 0;
        $checkedInGuestCounter = 0;
        $noShowCounter = 0;
        foreach ($event->getGuests() as $guest) {
            $guestCounter += ($guest->getPluses() + 1);
            if ($guest->getCheckInTime()) {
                $checkedInGuestCounter += ($guest->getCheckedInPluses() +1);
                if ($guest->getCheckedInPluses() < $guest->getPluses()) {
                    $noShowCounter += $guest->getPluses() - $guest->getCheckedInPluses();
                }
            }
        }
        $percentage = (100 / ($guestCounter) * $checkedInGuestCounter);
        $noShow = (100 / $guestCounter * $noShowCounter);

        return [
            'percentages' => [
                'percentage' => round($percentage),
                'noShowPercentage' => round($noShow),
            ],
            'counters' => [
                'totalExpectedGuests' => $guestCounter,
                'totalCheckedIn' => $checkedInGuestCounter,
                'totalNoShows' => $noShowCounter,
            ]
        ];
    }
}