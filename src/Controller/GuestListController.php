<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuestListController extends AbstractController
{
    private EventRepository $eventRepository;
    private EventService $eventService;

    public function __construct(EventRepository $eventRepository, EventService $eventService)
    {
        $this->eventRepository = $eventRepository;
        $this->eventService = $eventService;
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
        return $this->render('guest_list/event.html.twig', array_merge([
            'event' => $event,
        ], $this->eventService->getStatsForEvent($event)));
    }
}
