<?php

namespace Tests\AppBundle\Security\Mocker;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractTokenManagerProviderProphecyTest extends KernelTestCase
{
    /**
     * @return ObjectProphecy
     */
    public function getMockEntityManager()
    {
        return $this->prophesize(EntityManager::class);
    }

    /**
     * @param string $repositoryName
     * @return ObjectProphecy
     */
    public function getMockRepository($repositoryName = null)
    {
        return $this->prophesize($repositoryName ? $repositoryName : ObjectRepository::class);
    }

    /**
     * @param null $name
     * @return ObjectProphecy|mixed
     */
    public function getMock($name = null)
    {
        return $this->prophesize($name);
    }

    /**
     * @return ObjectProphecy
     */
    public function getMockLogger()
    {
        /** @var LoggerInterface|ObjectProphecy $logger */
        $logger = $this->getMock(LoggerInterface::class);
        // Stub
        $logger
            ->error(Argument::type('string'), Argument::type('array'))
            ->willReturn(true);

        $logger
            ->error(Argument::type('string'), Argument::type('array'))
            ->willReturn(true);

        // On initialise l'objet
        return $logger->reveal();
    }
}