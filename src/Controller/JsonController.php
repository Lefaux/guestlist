<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Guest;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        return $this->json([
            'id' => $event->getId(),
            'name' => $event->getName(),
        ]);
    }

    /**
     * @Route("/json/{event}/guests", name="app_json_get_guests", methods={"GET"})
     */
    public function getGuests(Event $event): Response
    {
        return $this->json($event->getGuests()->toArray());
    }

    /**
     * @Route("/json/checkin/{guest}", name="app_json_checkin", methods={"POST"})
     */
    public function checkIn(Guest $guest, Request $request): JsonResponse
    {
        $payload = [];
        if (($content = $request->getContent()) !== '') {
            $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        }
        $checkInTime = new \DateTime('now');
        try {
            $guest->setCheckInTime($checkInTime);
            $guest->setCheckedInPluses($payload['checkedInPluses'] ?: 0);
            $this->entityManager->persist($guest);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'stack' => $e->getTrace(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->json($guest);
    }
    /**
     * @Route("/json/checkout/{guest}", name="app_json_checkout", methods={"POST"})
     */
    public function checkOut(Guest $guest): JsonResponse
    {
        try {
            $guest->setCheckInTime(null);
            $guest->setCheckedInPluses(null);

            $this->entityManager->persist($guest);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'stack' => $e->getTrace(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->json($guest);
    }
}
