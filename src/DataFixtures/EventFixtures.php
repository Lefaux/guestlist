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
        $fixture = [];
        $fixture[] = [
            'name' => 'Event 1',
            'eventStart' => new \DateTime('2022-03-04 18:00:00'),
            'ident' => 'event1'
        ];
        $fixture[] = [
            'name' => 'Event 2',
            'eventStart' => new \DateTime('2023-12-04 21:00:00'),
            'ident' => 'event2'
        ];

        foreach ($fixture as $item) {
            $event = (new Event())
            ->setName($item['name'])
            ->setEventStart($item['eventStart']);
            $manager->persist($event);
            $this->addReference($item['ident'], $event);
        }
    }
}
