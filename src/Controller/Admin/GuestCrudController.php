<?php

namespace App\Controller\Admin;

use App\Entity\Guest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpKernel\KernelInterface;

class GuestCrudController extends AbstractCrudController
{
    private string $environment;

    public function __construct(KernelInterface $kernel)
    {
        $this->environment = $kernel->getEnvironment();
    }

    public static function getEntityFqcn(): string
    {
        return Guest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $titlePrefix = '';
        if ($this->environment === 'dev') {
            $titlePrefix = '[DEV] ';
        }
        return $crud
            ->setDefaultSort(['event.eventStart' => 'DESC'])
            ->setDateFormat('dd.MM.Y (eeee)')
            ->showEntityActionsInlined()
            ->setPageTitle('index', $titlePrefix . 'Guestlist Admin %entity_label_plural%')
            ->setPageTitle('edit', $titlePrefix . 'Guestlist Admin Edit %entity_label_plural%')
            ->setPageTitle('new', $titlePrefix . 'Guestlist Admin Add %entity_label_plural%')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            FormField::addPanel('Gast')->setIcon('fa fa-user'),
            TextField::new('firstName')->setColumns(6),
            TextField::new('lastName')->setColumns(6),


            AssociationField::new('event')->setColumns(6),
            NumberField::new('pluses')->setColumns(6),
            BooleanField::new('vip')->setColumns(3),

            FormField::addPanel('Check-In Information')->setIcon('fa fa-check'),
            DateTimeField::new('checkInTime')->setColumns(6)->hideOnIndex(),
            NumberField::new('checkedInPluses')->setColumns(6)->hideOnIndex(),
        ];
    }
}
