<?php

namespace WinkMurder\Bundle\GameBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WinkMurder\Bundle\GameBundle\Security\Factory;

class WinkMurderGameBundle extends Bundle {

    public function build(ContainerBuilder $container) {
        parent::build($container);
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new Factory());
    }

}