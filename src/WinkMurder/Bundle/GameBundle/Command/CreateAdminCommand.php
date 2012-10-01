<?php

namespace WinkMurder\Bundle\GameBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use WinkMurder\Bundle\GameBundle\Entity\Admin;

class CreateAdminCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('wink-murder:create-admin')
            ->setDescription('Creates an admin.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the admin.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();

        $admin = new Admin($input->getArgument('password'));
        $admin->setName($input->getArgument('name'));

        $entityManager->persist($admin);
        $entityManager->flush();
    }

}