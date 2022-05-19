<?php

namespace App\Tests;

use App\DataFixtures\EventFixtures;
use App\DataFixtures\GuestFixture;
use App\Entity\Guest;
use App\Enum\CheckinStatusEnum;
use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonControllerTest extends WebTestCase
{

    protected function loadFixtures(array $fixtures): AbstractExecutor
    {
        /** @var AbstractDatabaseTool $databaseTollCollection */
        $abstractDatabaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        return $abstractDatabaseTool->loadFixtures($fixtures);
    }

    public function testBaseStats(): void
    {
        $client = static::createClient();
        $fixtures = $this->loadFixtures([
            EventFixtures::class,
            GuestFixture::class
        ])->getReferenceRepository();

        $client->xmlHttpRequest('POST', '/json/stats/1');

        self::assertResponseIsSuccessful();
        $expectedArray = [
            'percentages' => [
                'percentage' => 20,
                'noShowPercentage' => 37
            ],
            'counters' => [
                'totalExpectedGuests' => 41,
                'totalCheckedIn' => 8,
                'totalNoShows' => 15
            ]
        ];
        $expected = json_encode($expectedArray);
        $this->assertSame($expected, $client->getResponse()->getContent());
    }

    public function testCancellation()
    {
        $client = static::createClient();
        $entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();
        $repo = $entityManager->getRepository(Guest::class);
        $fixtures = $this->loadFixtures([
            EventFixtures::class,
            GuestFixture::class
        ])->getReferenceRepository();
        // Get record
        /** @var Guest $guestToWorkOn */
        $guestToWorkOn = $fixtures->getReference('event1guest_1');
        $expected = CheckinStatusEnum::CANCELLED;
        $client->xmlHttpRequest('POST', '/json/cancel/' . $guestToWorkOn->getId());
        $actual = $repo->find($guestToWorkOn->getId());
        $this->assertSame($expected, $actual->getCheckInStatus());
    }


}
