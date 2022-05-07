<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Guest;
use App\Form\GuestType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['eventStart' => 'DESC'])
            ->setDateFormat('dd.MM.Y (eeee)')
            ->showEntityActionsInlined()
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $downloadCSV = Action::new('downloadCSV', 'Download CSV', 'fa fa-file-invoice')
            ->linkToCrudAction('downloadCSVAction')
            ->setCssClass('btn btn-primary')
        ;
        $actions
            ->add(Crud::PAGE_INDEX, $downloadCSV)
        ;
        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('eventStart')->setColumns(3),
            TextField::new('name')->setColumns(9),
            CollectionField::new('guests')
                ->allowAdd(true)
                ->allowDelete()
                ->setEntryType(GuestType::class)
                ->setEntryIsComplex(true)
                ->setSortable(false)

        ];
    }

    public function downloadCSVAction(AdminContext $context): Response
    {
        $futureEvent = true;
        /** @var Event $event */
        $event = $context->getEntity()->getInstance();
        if ($event->getEventStart() < new \DateTime('now')) {
            $futureEvent = false;
        }
        $csvString = '';
        // Add Header Columns
        $header = [
            'VIP',
            'Vorname',
            'Nachname',
            'Plus'
        ];
        if (!$futureEvent) {
            $header[] = 'Gäste real';
            $header[] = 'Eingecheckt am';
            $header[] = 'Status';
        }
        $csvString .= $this->compileCSVRow($header);
        /** @var Guest $guest */
        foreach ($event->getGuests() as $guest) {
            $guestRow = [
                $guest->getVip() ? 'VIP': '',
                $guest->getFirstName(),
                $guest->getLastName(),
                $guest->getPluses()
            ];
            if (!$futureEvent) {
                $guestRow[] = $guest->getCheckedInPluses();
                $guestRow[] = $guest->getCheckInTime() ? $guest->getCheckInTime()->format('d.m.Y H:i') : '-';
                // @todo Fix this once #31 is merged by Andy
                $guestRow[] = 'FIXED WITH #31';
            }
            $csvString .= $this->compileCSVRow($guestRow);
        }
        $response = new Response($csvString);
        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . str_replace(' ', '_', $event->getName()) . '.csv');
        return $response;
    }

    private function compileCSVRow(array $data): string
    {
        $line = '';
        foreach ($data as $value) {
            $line .= '"' . $value . '",';
        }
        $line .= "\n";
        return $line;
    }
}
