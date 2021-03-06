<?php

namespace BlueBear\EngineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BlueBearEngineExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        // engine allowed events
        $container->setParameter('bluebear.engine.events', $config['events']);
        $container->setParameter('bluebear.game.entity_type', $config['game']['entity_type']);
        $container->setParameter('bluebear.game.attribute', $config['game']['attribute']);
        $container->setParameter('bluebear.game.behaviors', $config['game']['behaviors']);
    }
}
