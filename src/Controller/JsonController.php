<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Guest;
use App\Enum\CheckinStatusEnum;
use App\Service\EventService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EventService $eventService;

    public function __construct(EntityManagerInterface $entityManager, EventService $eventService)
    {
        $this->entityManager = $entityManager;
        $this->eventService = $eventService;
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
            try {
                $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
        }
        $checkInTime = new DateTime('now');
        try {
            $guest->setCheckInTime($checkInTime);
            $guest->setCheckedInPluses($payload['checkedInPluses'] ?: 0);
            $guest->setCheckInStatus(CheckinStatusEnum::CHECKED_IN);
            if ($guest->getPluses() > $guest->getCheckedInPluses()) {
                $guest->setCheckInStatus(CheckinStatusEnum::CHECKED_IN_WITH_NOSHOWS);
            }
            $this->entityManager->persist($guest);
            $this->entityManager->flush();
        } catch (Exception $e) {
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
            $guest->setCheckInStatus(CheckinStatusEnum::OPEN);

            $this->entityManager->persist($guest);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return $this->json([
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'stack' => $e->getTrace(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->json($guest);
    }

    /**
     * @Route("/json/stats/{event}", name="app_json_stats", methods={"POST"})
     */
    public function getStats(Event $event): Response
    {
        return $this->json($this->eventService->getStatsForEvent($event));
    }

    /**
     * @Route("/json/cancel/{guest}", name="app_json_cancel", methods={"POST"})
     */
    public function cancelGuest(Guest $guest): JsonResponse
    {
        $checkInTime = new DateTime('now');
        try {
            $guest->setCheckInTime($checkInTime);
            $guest->setCheckedInPluses(0);
            $guest->setCheckInStatus(CheckinStatusEnum::CANCELLED);
            $this->entityManager->persist($guest);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return $this->json([
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'stack' => $e->getTrace(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->json($guest);
    }
}
