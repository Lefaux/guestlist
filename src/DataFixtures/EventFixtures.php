<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $event = new Event();
            $event->setName($faker->words(4, true));
            $event->setEventStart($faker->dateTime());
            $manager->persist($event);
            $this->addReference('event_' . $i, $event);
        }
    }
}
