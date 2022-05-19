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
        $counter = 1;
        $faker = Factory::create();
        /** @var Event $event */
        $event = $this->getReference('event1');
        $checkInOption = CheckinStatusEnum::OPEN;
        // NON VIP, not checked in
        $guest = (new Guest())->setFirstName('Husel')->setLastName('Pusel')->setEvent($event);
        $guest->setPluses(1);
        $guest->setVip(false);
        $guest->setCheckInStatus($checkInOption);
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;

        $guest = (new Guest())->setFirstName($faker->firstName)->setEvent($event);
        $guest->setPluses(1);
        $guest->setVip(false);
        $guest->setCheckInStatus($checkInOption);
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;

        $guest = (new Guest())->setLastName($faker->lastName)->setEvent($event);
        $guest->setPluses(1);
        $guest->setVip(false);
        $guest->setCheckInStatus($checkInOption);
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;
        // VIP, not checked in
        $guest = (new Guest())->setFirstName($faker->firstName)->setLastName($faker->lastName)->setEvent($event);
        $guest->setPluses(1);
        $guest->setVip(true);
        $guest->setCheckInStatus($checkInOption);
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;

        $guest = (new Guest())->setFirstName($faker->firstName)->setEvent($event);
        $guest->setPluses(1);
        $guest->setVip(true);
        $guest->setCheckInStatus($checkInOption);
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;

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
        /**
         * Add guests who are checked in
         */
        // Full Name plus 0, VIP, not checked in
        $guest = (new Guest())->setFirstName($faker->firstName)->setLastName($faker->lastName)->setEvent($event);
        $guest->setPluses(2);
        $guest->setVip(true);
        $guest->setCheckInStatus(CheckinStatusEnum::CHECKED_IN);
        $guest->setCheckedInPluses(2);
        $guest->setCheckInTime(new \DateTime('2022-04-05 19:04:31'));
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;
        // First Name plus 1, VIP, not checked in
        $guest = (new Guest())->setFirstName($faker->firstName)->setEvent($event);
        $guest->setPluses(3);
        $guest->setVip(false);
        $guest->setCheckInStatus(CheckinStatusEnum::CHECKED_IN_WITH_NOSHOWS);
        $guest->setCheckedInPluses(0);
        $guest->setCheckInTime(new \DateTime('2022-04-05 19:05:31'));
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;
        // Last Name plus 1, VIP, not checked in
        $guest = (new Guest())->setLastName($faker->lastName)->setEvent($event);
        $guest->setPluses(3);
        $guest->setVip(false);
        $guest->setCheckInStatus(CheckinStatusEnum::CHECKED_IN_WITH_NOSHOWS);
        $guest->setCheckedInPluses(1);
        $guest->setCheckInTime(new \DateTime('2022-04-05 19:06:31'));
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;
        // Last Name plus 1, VIP, not checked in
        $guest = (new Guest())->setLastName($faker->lastName)->setEvent($event);
        $guest->setPluses(3);
        $guest->setVip(false);
        $guest->setCheckInStatus(CheckinStatusEnum::CANCELLED);
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;
        // Last Name plus 1, VIP, not checked in
        $guest = (new Guest())->setLastName($faker->lastName)->setEvent($event);
        $guest->setPluses(3);
        $guest->setVip(false);
        $guest->setCheckInStatus(CheckinStatusEnum::CANCELLED);
        $manager->persist($guest);
        $this->addReference('event1guest_' . $counter, $guest);
        $counter++;
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixtures::class
        ];
    }
}
