<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{
    /**
     * @Route("/json/{event}", name="app_json")
     */
    public function index(Event $event): Response
    {
        $guests = [];
        foreach ($event->getGuests() as $guest) {
            $guests[] = [
                'id' => $guest->getId(),
                'firstName' => $guest->getFirstName(),
                'lastName' => $guest->getLastName(),
                'pluses' => $guest->getPluses(),
                'checkedInTime' => $guest->getCheckInTime()
            ];
        }
        return $this->json([
            'event' => [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'guests' => $guests
            ]
        ]);
    }
}
