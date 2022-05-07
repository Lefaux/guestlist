<?php

namespace App\Command;

use App\Enum\CheckinStatusEnum;
use App\Repository\GuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixDatabaseDataCommand extends Command
{
    protected static $defaultName = 'app:fix-database-data';
    protected static $defaultDescription = 'Attempt to fix inconsistencies in the database data';
    private GuestRepository $guestRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(GuestRepository $guestRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->guestRepository = $guestRepository;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Trying to fix data');

        $guests = $this->guestRepository->findAll();
        $io->writeln('Found ' . count($guests) . ' guests to work on');
        foreach ($guests as $guest) {
            if ($guest->getCheckInStatus() === 'OPEN') {
                if (!$guest->getCheckInTime()) {
                    $guest->setCheckInStatus(CheckinStatusEnum::OPEN);
                    $io->write('O');
                }
                if ($guest->getCheckInTime() && $guest->getPluses() > $guest->getCheckedInPluses()) {
                    $guest->setCheckInStatus(CheckinStatusEnum::CHECKED_IN_WITH_NOSHOWS);
                    $io->write('N');
                }
                if ($guest->getCheckInTime() && $guest->getPluses() === $guest->getCheckedInPluses()) {
                    $guest->setCheckInStatus(CheckinStatusEnum::CHECKED_IN);
                    $io->write('C');
                }
            }
            $this->entityManager->persist($guest);
            $this->entityManager->flush();
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
