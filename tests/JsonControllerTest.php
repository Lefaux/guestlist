<?php

namespace App\Tests;

use App\DataFixtures\EventFixtures;
use App\DataFixtures\GuestFixture;
use App\Entity\Guest;
use App\Enum\CheckinStatusEnum;
use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private AbstractExecutor $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->fixtures = $this->loadFixtures([
            EventFixtures::class,
            GuestFixture::class
        ]);
    }

    protected function loadFixtures(array $fixtures): AbstractExecutor
    {
        /** @var AbstractDatabaseTool $databaseTollCollection */
        $abstractDatabaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        return $abstractDatabaseTool->loadFixtures($fixtures);
    }

    public function testBaseStats(): void
    {
        $event = $this->fixtures->getReferenceRepository()->getReference('event1');
        $this->client->xmlHttpRequest('POST', '/json/stats/' . $event->getId());

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
        $expected = json_encode($expectedArray, JSON_THROW_ON_ERROR);
        $this->assertSame($expected, $this->client->getResponse()->getContent());
    }

    public function testCancellation()
    {
        $repo = $this->entityManager->getRepository(Guest::class);

        // Get record
        /** @var Guest $guestToWorkOn */
        $guestToWorkOn = $this->fixtures->getReferenceRepository()->getReference('event1guest_1');
        $expected = CheckinStatusEnum::CANCELLED;
        $this->client->xmlHttpRequest('POST', '/json/cancel/' . $guestToWorkOn->getId());
        $actual = $repo->find($guestToWorkOn->getId());
        $this->assertSame($expected, $actual->getCheckInStatus());
    }


}
