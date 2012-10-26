<?php

namespace WinkMurder\Bundle\GameBundle\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class Factory extends AbstractFactory {

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId) {
        $provider = 'wink_murder.security.account_provider' . $id;

        $container
            ->setDefinition($provider, new DefinitionDecorator('wink_murder.security.account_provider'))
            ->replaceArgument(0, new Reference($userProviderId));

        return $provider;
    }

    protected function getListenerId() {
        return 'wink_murder.security.account_listener';
    }

    protected function createListener($container, $id, $config, $userProvider) {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $container
                ->getDefinition($listenerId)
                ->addArgument(new Reference('doctrine.orm.entity_manager'));

        return $listenerId;
    }

    public function getPosition() {
        return 'pre_auth';
    }

    public function getKey() {
        return 'account';
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint) {
        $entryPointId = 'security.authentication.form_entry_point.' . $id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('security.authentication.form_entry_point'))
            ->addArgument(new Reference('security.http_utils'))
            ->addArgument($config['login_path'])
            ->addArgument($config['use_forward'])
        ;

        return $entryPointId;
    }


}