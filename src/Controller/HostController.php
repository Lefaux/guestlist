<?php

namespace App\Controller;

use App\Entity\Event;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HostController extends AbstractController
{

    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * @Route("/host/{event}", name="app_host")
     */
    public function index(Event $event): Response
    {
        return $this->render('host/index.html.twig', [
            'event' => $event,
            'stats' => $this->eventService->getStatsForEvent($event)
        ]);
    }
}
