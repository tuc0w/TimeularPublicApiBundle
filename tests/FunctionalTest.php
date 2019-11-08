<?php

namespace Tuc0w\TimeularPublicApiBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Tuc0w\TimeularPublicApiBundle\Service\Client as TimeularClient;
use Tuc0w\TimeularPublicApiBundle\Tuc0wTimeularPublicApiBundle;

class FunctionalTest extends TestCase
{
    public function testServiceWiring()
    {
        $kernel = new Tuc0wTimeularPublicApiTestingKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $timeular = $container->get('tuc0w_timeular_public_api.client');
        $this->assertInstanceOf(TimeularClient::class, $timeular);
    }
}

class Tuc0wTimeularPublicApiTestingKernel extends Kernel
{
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return [
            new Tuc0wTimeularPublicApiBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('tuc0w_timeular_public_api', $this->config);
        });
    }
}
