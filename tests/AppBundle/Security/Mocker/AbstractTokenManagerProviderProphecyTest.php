<?php

namespace Tests\AppBundle\Security\Mocker;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractTokenManagerProviderProphecyTest extends KernelTestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockEntityManager()
    {
        return $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param string $repositoryName
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockRepository($repositoryName = null)
    {
        return $this
            ->getMockBuilder($repositoryName ? $repositoryName : ObjectRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param null $name
     * @return mixed
     */
    public function getMock($name = null)
    {
        return $this
            ->getMockBuilder($name)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockLogger()
    {
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMock(LoggerInterface::class);
        $logger
            ->method('error')
            ->withAnyParameters()
            ->will($this->returnValue(true));
        $logger
            ->method('info')
            ->withAnyParameters()
            ->will($this->returnValue(true));

        return $logger;
    }
}