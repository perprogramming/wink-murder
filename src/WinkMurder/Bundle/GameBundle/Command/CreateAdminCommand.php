<?php

namespace WinkMurder\Bundle\GameBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use WinkMurder\Bundle\GameBundle\Entity\Player;

class CreateAdminCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('wink-murder:create-admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $validator = $this->getContainer()->get('validator');

        $admin = new Player();
        $admin->addRole('ROLE_ADMIN');

        $dialog = $this->getHelperSet()->get('dialog');

        foreach (array(
            'name' => 'Name',
            'username' => 'Benutzername',
            'password' => 'Passwort'
        ) as $property => $label)
        $dialog->askAndValidate($output, "<question>$label:</question>", function($value) use ($validator, $admin, $property) {
            $setter = 'set' . ucfirst($property);
            $admin->$setter($value);
            if (($errors = $validator->validateProperty($admin, $property)) && count($errors)) {
                throw new \Exception($errors[0]->getMessage());
            }
        });

        $entityManager->persist($admin);
        $entityManager->flush();
    }

}