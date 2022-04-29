<?php

namespace App\Controller\Admin;

use App\Entity\Guest;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GuestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Guest::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            FormField::addPanel('Gast')->setIcon('fa fa-user'),
            TextField::new('firstName')->setColumns(6),
            TextField::new('lastName')->setColumns(6),


            AssociationField::new('event')->setColumns(6),
            NumberField::new('pluses')->setColumns(6),

            FormField::addPanel('Check-In Information')->setIcon('fa fa-check'),
            DateTimeField::new('checkInTime')->setColumns(6)->hideOnIndex(),
            NumberField::new('checkedInPluses')->setColumns(6)->hideOnIndex(),
        ];
    }
}
