<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\GuestType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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

}
