<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Enum\CheckinStatusEnum;

class EventService
{
    public function getStatsForEvent(Event $event): array
    {
        $guestCounter = 0;
        $checkedInGuestCounter = 0;
        $noShowCounter = 0;
        foreach ($event->getGuests() as $guest) {
            $guestCounter += ($guest->getPluses() + 1);
            switch ($guest->getCheckInStatus()) {
                case CheckinStatusEnum::CHECKED_IN:
                    $checkedInGuestCounter += ($guest->getCheckedInPluses() +1);
                    break;
                case CheckinStatusEnum::CHECKED_IN_WITH_NOSHOWS:
                    $noShowCounter += $guest->getPluses() - $guest->getCheckedInPluses();
                    $checkedInGuestCounter += ($guest->getCheckedInPluses() +1);
                    break;
                case CheckinStatusEnum::CANCELLED:
                    $noShowCounter += ($guest->getPluses() +1);
                    break;
            }
        }
        $percentage = 0;
        $noShow = 0;
        if ($guestCounter > 0 && $checkedInGuestCounter > 0) {
            $percentage = (100 / ($guestCounter) * $checkedInGuestCounter);
            $noShow = (100 / $guestCounter * $noShowCounter);
        }

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