<?php

namespace CustomSearchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Resource\FileResource;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CustomSearchExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Loads WebsiteBundle configuration.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependFile($container, 'ezpublish', 'ez_view_templates.yml');
    }

    private function prependFile($container, $section, $configFile)
    {
        $configFile = __DIR__.'/../Resources/config/'.$configFile;
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig($section, $config);
        $container->addResource(new FileResource($configFile));
    }
}
