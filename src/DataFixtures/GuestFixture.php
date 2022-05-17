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
        $checkInOptions = CheckinStatusEnum::getAvailableOptions();
        $counter = 1;
        $faker = Factory::create();
        /** @var Event $event */
        $event = $this->getReference('event1');
        foreach ($checkInOptions as $checkInOption) {
            if ($checkInOption === CheckinStatusEnum::CHECKED_IN) {

            }
            // Full Name plus 1, no VIP, not checked in
            $guest = (new Guest())->setFirstName($faker->firstName)->setLastName($faker->lastName)->setEvent($event);
            $guest->setPluses(1);
            $guest->setVip(false);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // First Name plus 1, no VIP, not checked in
            $guest = (new Guest())->setFirstName($faker->firstName)->setEvent($event);
            $guest->setPluses(1);
            $guest->setVip(false);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // Last Name plus 1, no VIP, not checked in
            $guest = (new Guest())->setLastName($faker->lastName)->setEvent($event);
            $guest->setPluses(1);
            $guest->setVip(false);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // Full Name plus 1, VIP, not checked in
            $guest = (new Guest())->setFirstName($faker->firstName)->setLastName($faker->lastName)->setEvent($event);
            $guest->setPluses(1);
            $guest->setVip(true);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // First Name plus 1, VIP, not checked in
            $guest = (new Guest())->setFirstName($faker->firstName)->setEvent($event);
            $guest->setPluses(1);
            $guest->setVip(true);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // Last Name plus 1, VIP, not checked in
            $guest = (new Guest())->setLastName($faker->lastName)->setEvent($event);
            $guest->setPluses(1);
            $guest->setVip(true);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // Full Name plus 0, VIP, not checked in
            $guest = (new Guest())->setFirstName($faker->firstName)->setLastName($faker->lastName)->setEvent($event);
            $guest->setPluses(0);
            $guest->setVip(true);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // First Name plus 1, VIP, not checked in
            $guest = (new Guest())->setFirstName($faker->firstName)->setEvent($event);
            $guest->setPluses(0);
            $guest->setVip(true);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // Last Name plus 1, VIP, not checked in
            $guest = (new Guest())->setLastName($faker->lastName)->setEvent($event);
            $guest->setPluses(0);
            $guest->setVip(true);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // Full Name plus 0, VIP, not checked in
            $guest = (new Guest())->setFirstName($faker->firstName)->setLastName($faker->lastName)->setEvent($event);
            $guest->setPluses(0);
            $guest->setVip(false);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // First Name plus 1, VIP, not checked in
            $guest = (new Guest())->setFirstName($faker->firstName)->setEvent($event);
            $guest->setPluses(0);
            $guest->setVip(false);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
            // Last Name plus 1, VIP, not checked in
            $guest = (new Guest())->setLastName($faker->lastName)->setEvent($event);
            $guest->setPluses(0);
            $guest->setVip(false);
            $guest->setCheckInStatus($checkInOption);
            $manager->persist($guest);
            $this->addReference('event1guest_' . $counter, $guest);
            $counter++;
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
