<?php

namespace WinkMurder\Bundle\GameBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class WinkMurderGameExtension extends Extension {

    public function load(array $config, ContainerBuilder $container) {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new XmlFileLoader($container, $locator);
        $loader->load('services.xml');
    }

}