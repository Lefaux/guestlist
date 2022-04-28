<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuestListController extends AbstractController
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @Route("/", name="app_guest_list")
     */
    public function index(): Response
    {
        $events = $this->eventRepository->findAll();
        return $this->render('guest_list/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/event/{event}", name="app_guest_list_event")
     */
    public function event(Event $event): Response
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
        return $this->render('guest_list/event.html.twig', [
            'event' => $event,
            'percentage' => round($percentage),
            'noShowPercentage' => round($noShow),
            'counters' => [
                'totalExpectedGuests' => $guestCounter,
                'totalCheckedIn' => $checkedInGuestCounter,
                'totalNoShows' => $noShowCounter,
            ]
        ]);
    }
}
