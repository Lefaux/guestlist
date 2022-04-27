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
        return $this->render('guest_list/event.html.twig', [
            'event' => $event,
        ]);
    }
}
