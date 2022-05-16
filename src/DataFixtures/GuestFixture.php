<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Guest;
use App\Enum\CheckinStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class GuestFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            /** @var Event $event */
            $event = $this->getReference('event_' . $i);
            for ($c = 0; $c < $faker->numberBetween(3, 40); $c++) {
                $guest = new Guest();
                $guest->setFirstName($faker->firstName);
                $guest->setLastName($faker->lastName);
                $guest->setPluses($faker->numberBetween(0,4));
                $guest->setVip($faker->boolean);
                $guest->setCheckInStatus(CheckinStatusEnum::OPEN);
                $guest->setEvent($event);
                $manager->persist($guest);
                $this->addReference('event_' . $i . 'guest_' . $c, $guest);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixtures::class
        ];
    }
}
