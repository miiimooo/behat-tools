<?php

namespace miiimooo\BehatTools\MiiimoooExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\ServiceContainer\ServiceProcessor;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MiiimoooExtension implements ExtensionInterface
{

  /**
   * Extension configuration ID.
   */
    const CONFIG_ID = 'miiimooo';

  /**
   * Initializes compiler pass.
   *
   * @param null|ServiceProcessor $processor
   */
    public function __construct(ServiceProcessor $processor = null)
    {
    }

  /**
   * {@inheritDoc}
   */
    public function getConfigKey()
    {
        return self::CONFIG_ID;
    }

  /**
   * {@inheritDoc}
   */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

  /**
   * {@inheritDoc}
   */
    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.yml');
    }

  /**
   * {@inheritDoc}
   */
    public function process(ContainerBuilder $container)
    {
    }

  /**
   * {@inheritDoc}
   */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

}
