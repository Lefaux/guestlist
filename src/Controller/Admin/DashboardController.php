<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Guest;
use App\Repository\EventRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $events = [];
        $allEvents = $this->eventRepository->findAll();
        foreach ($allEvents as $event) {
            $events[] = [
                'name' => $event->getName(),
                'numberOfGuests' => count($event->getGuests())
            ];
        }
        return $this->render('dashboard.html.twig', ['events' => $events]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Guestlist');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Events', 'fas fa-list', Event::class);
        yield MenuItem::linkToCrud('Guests', 'fas fa-user', Guest::class);
    }
}
