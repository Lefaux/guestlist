<?php

namespace App\Tests;

use App\Entity\Event;
use App\Entity\Guest;
use App\Enum\CheckinStatusEnum;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventServiceTest extends KernelTestCase
{
    public function testEventStatsWithOneGuest(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var EventService $eventService */
        $eventService = static::getContainer()->get(EventService::class);
        // Set up Mock Data
        $event = new Event();
        $event->setName('Testcase');
        $event->setEventStart(new \DateTime('now'));
        $guestA = new Guest();
        $guestA->setFirstName('Husel');
        $guestA->setLastName('Pusel');
        $guestA->setPluses(1);
        $event->addGuest($guestA);
        $eventStats = $eventService->getStatsForEvent($event);
        $expectedResult = [
            'percentages' => [
                'percentage' => 0.0,
                'noShowPercentage' => 0.0
            ],
            'counters' => [
                'totalExpectedGuests' => 2,
                'totalCheckedIn' => 0,
                'totalNoShows' => 0
            ]
        ];
        $this->assertSame($expectedResult, $eventStats);
    }

    public function testEventStatsWithCheckedInGuest(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var EventService $eventService */
        $eventService = static::getContainer()->get(EventService::class);
        // Set up Mock Data
        $event = new Event();
        $event->setName('Testcase');
        $event->setEventStart(new \DateTime('now'));
        $guestA = new Guest();
        $guestA->setFirstName('Husel');
        $guestA->setLastName('Pusel');
        $guestA->setPluses(1);
        $guestA->setCheckInStatus(CheckinStatusEnum::CHECKED_IN);
        $guestA->setCheckedInPluses(1);
        $event->addGuest($guestA);
        $eventStats = $eventService->getStatsForEvent($event);
        $expectedResult = [
            'percentages' => [
                'percentage' => 100.0,
                'noShowPercentage' => 0.0
            ],
            'counters' => [
                'totalExpectedGuests' => 2,
                'totalCheckedIn' => 2,
                'totalNoShows' => 0
            ]
        ];
        $this->assertSame($expectedResult, $eventStats);
    }

    public function testEventStatsWithCheckedInGuestPlusNoShows(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var EventService $eventService */
        $eventService = static::getContainer()->get(EventService::class);
        // Set up Mock Data
        $event = new Event();
        $event->setName('Testcase');
        $event->setEventStart(new \DateTime('now'));
        $guestA = new Guest();
        $guestA->setFirstName('Husel');
        $guestA->setLastName('Pusel');
        $guestA->setPluses(1);
        $guestA->setCheckInStatus(CheckinStatusEnum::CHECKED_IN_WITH_NOSHOWS);
        $guestA->setCheckedInPluses(0);
        $event->addGuest($guestA);
        $eventStats = $eventService->getStatsForEvent($event);
        $expectedResult = [
            'percentages' => [
                'percentage' => 50.0,
                'noShowPercentage' => 50.0
            ],
            'counters' => [
                'totalExpectedGuests' => 2,
                'totalCheckedIn' => 1,
                'totalNoShows' => 1
            ]
        ];
        $this->assertSame($expectedResult, $eventStats);
    }

    public function testEventStatsWithCheckedInGuestsPlusNoShows(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var EventService $eventService */
        $eventService = static::getContainer()->get(EventService::class);
        // Set up Mock Data
        $event = new Event();
        $event->setName('Testcase');
        $event->setEventStart(new \DateTime('now'));

        $guestA = new Guest();
        $guestA->setFirstName('Husel');
        $guestA->setLastName('Pusel');
        $guestA->setPluses(1);
        $guestA->setCheckInStatus(CheckinStatusEnum::CHECKED_IN_WITH_NOSHOWS);
        $guestA->setCheckedInPluses(0);
        $event->addGuest($guestA);

        $guestB = new Guest();
        $guestB->setFirstName('Bla');
        $guestB->setLastName('Fasel');
        $guestB->setPluses(3);
        $guestB->setCheckInStatus(CheckinStatusEnum::CHECKED_IN);
        $guestB->setCheckedInPluses(3);
        $event->addGuest($guestB);

        $eventStats = $eventService->getStatsForEvent($event);
        $expectedResult = [
            'percentages' => [
                'percentage' => 83.0,
                'noShowPercentage' => 17.0
            ],
            'counters' => [
                'totalExpectedGuests' => 6,
                'totalCheckedIn' => 5,
                'totalNoShows' => 1
            ]
        ];
        $this->assertSame($expectedResult, $eventStats);
    }
}
