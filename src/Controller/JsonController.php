<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Guest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/json/{event}", name="app_json")
     */
    public function index(Event $event): JsonResponse
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

    /**
     * @Route("/json/checkin/{guest}", name="app_json_checkin")
     */
    public function checkIn(Guest $guest): JsonResponse
    {
        $status = 'NACK';
        try {
            $guest->setCheckInTime(new \DateTime('now'));
            $this->entityManager->persist($guest);
            $this->entityManager->flush();
            $status = 'ACK';
        } catch (\Exception $e) {
        }
        return $this->json([
            'status' => $status
        ]);
    }
    /**
     * @Route("/json/checkout/{guest}", name="app_json_checkout")
     */
    public function checkOut(Guest $guest): JsonResponse
    {
        $status = 'NACK';
        try {
            $guest->setCheckInTime(null);
            $this->entityManager->persist($guest);
            $this->entityManager->flush();
            $status = 'ACK';
        } catch (\Exception $e) {
        }
        return $this->json([
            'status' => $status
        ]);
    }
}
