<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Guest;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{
    private EventRepository $eventRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/import", name="admin_import")
     */
    public function index(): Response
    {
        $events = $this->eventRepository->findAll();
        return $this->render('admin/import.html.twig', ['events' => $events]);
    }

    /**
     * @Route("/admin/import/upload", name="admin_import_upload")
     */
    public function upload(Request $request): Response
    {
        try {
            $event = $this->processFile($request);
        } catch (Exception $e) {
            return $this->render('admin/import-exception.html.twig', [
                'errors' => $e->getMessage()
            ]);
        }
        return $this->render('admin/import-result.html.twig', [
            'event' => $event
        ]);
    }

    private function processFile(Request $request): Event
    {
        $eventId = $request->get('eventId');

        $event = $this->eventRepository->find((int)$eventId);
        if (!$event) {
            throw new RuntimeException('Event could not be found - make sure you selected an event');
        }
        $fileInTempDir = $request->files->get('csvfile');
        if (!$fileInTempDir) {
            throw new RuntimeException('No guestlist file provided - please upload it');
        }
        $fileData = $fileInTempDir->getContent();
        $lines = explode(PHP_EOL, $fileData);
        $guestList = [];
        foreach ($lines as $line) {
            $guestList[] = str_getcsv($line);
        }
        /**
         * Remove previous entries
         */
        foreach ($event->getGuests() as  $oldGuest) {
            $event->removeGuest($oldGuest);
        }
        foreach ($guestList as $index => $item) {
            if ($index > 0) {
                $guest = new Guest();
                $guest->setVip(false);
                if (trim($item[0]) === 'VIP') {
                    $guest->setVip(true);
                }
                $guest->setFirstName($item[1]);
                $guest->setEvent($event);
                $guest->setLastName($item[2]);
                $guest->setPluses((int)$item[3]);
                $event->addGuest($guest);
                $this->entityManager->persist($guest);
                $this->entityManager->flush();
            }
        }
        return $event;
    }
}
