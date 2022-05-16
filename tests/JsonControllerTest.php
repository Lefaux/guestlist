<?php

namespace App\Tests;

use App\DataFixtures\EventFixtures;
use App\DataFixtures\GuestFixture;
use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonControllerTest extends WebTestCase
{

    protected $client;

    public function setUp(): void
    {
        parent::setUp();
        static::bootKernel([]);
        $this->client = self::$kernel->getContainer('test.client');
    }

    protected function loadFixtures(array $fixtures): AbstractExecutor
    {
        /** @var AbstractDatabaseTool $databaseTollCollection */
        $abstractDatabaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        return $abstractDatabaseTool->loadFixtures($fixtures);
    }

    public function testSomething(): void
    {
//        self::ensureKernelShutdown();
        $fixtures = $this->loadFixtures([
            EventFixtures::class,
            GuestFixture::class
        ])->getReferenceRepository();

        $this->client->xmlHttpRequest('POST', '/json/stats/1');

        self::assertResponseIsSuccessful();
        $expected = '';
        $this->assertSame($expected, $client->getResponse()->getContent());
    }


}
