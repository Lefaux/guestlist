<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpKernel\KernelInterface;

class DatabasePrimer
{
    public static function prime(KernelInterface $kernel): void
    {
        // Make sure we are in the test environment
        if ($kernel->getEnvironment() !== 'test') {
            throw new \LogicException('Primer must be executed in the test environment');
        }

        // Get the entity manager from the service container
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Recreate the database
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);

        // If you are using the Doctrine Fixtures Bundle you could load these here
    }
}