<?php

namespace Tuc0w\TimeularPublicApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


/**
 * Bundle Extension
 * 
 * @package Tuc0w\TimeularPublicApiBundle\DependencyInjection
 * @author Andreas "tuc0w" Behrend <andreasbehrend@gmail.com>
 */
class Tuc0wTimeularPublicApiExtension extends Extension {

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        
        if ($container->hasDefinition('tuc0w_timeular_public_api.client')) {
            $definition = $container->getDefinition('tuc0w_timeular_public_api.client');

            if (isset($config['timeular']['api_base_url'])) {
                $definition
                    ->addMethodCall('setApiBaseUrl', [$config['timeular']['api_base_url']]);
            }

            if (isset($config['timeular']['api_key'])) {
                $definition
                    ->addMethodCall('setApiKey', [$config['timeular']['api_key']]);
            }

            if (isset($config['timeular']['api_secret'])) {
                $definition
                    ->addMethodCall('setApiSecret', [$config['timeular']['api_secret']]);
            }

            if (isset($config['timeular']['api_timeout'])) {
                $definition
                    ->addMethodCall('setApiTimeout', [$config['timeular']['api_timeout']]);
            }

            if (isset($config['timeular']['api_version'])) {
                $definition
                    ->addMethodCall('setApiVersion', [$config['timeular']['api_version']]);
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias() {
        return 'tuc0w_timeular_public_api';
    }
}