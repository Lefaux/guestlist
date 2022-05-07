<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Guest;
use App\Repository\EventRepository;
use App\Service\EventService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    private EventRepository $eventRepository;
    private EventService $eventService;

    public function __construct(EventRepository $eventRepository, EventService $eventService)
    {
        $this->eventRepository = $eventRepository;
        $this->eventService = $eventService;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $events = [];
        $allEvents = $this->eventRepository->findBy([], ['eventStart' => 'DESC']);
        foreach ($allEvents as $event) {
            $events[] = [
                'name' => $event->getName(),
                'eventStart' => $event->getEventStart(),
                'stats' => $this->eventService->getStatsForEvent($event)
            ];
        }
        return $this->render('admin/dashboard.html.twig', ['events' => $events]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Guestlist');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Records');
        yield MenuItem::linkToCrud('Events', 'fa fa-calendar', Event::class);
        yield MenuItem::linkToCrud('Guests', 'fa fa-user', Guest::class);
        yield MenuItem::section('Tools');
        yield MenuItem::linkToRoute('Importer', 'fa fa-file-import', 'admin_import');
        yield MenuItem::section();
        yield MenuItem::section();
        yield MenuItem::section();
        yield MenuItem::section();
        yield MenuItem::linkToRoute('Back to site', 'fas fa-chevron-left', 'app_guest_list');
    }
}
