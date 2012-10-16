<?php

namespace WinkMurder\Bundle\GameBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use WinkMurder\Bundle\GameBundle\Entity\AdminAccount;

class CreateAdminCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('wink-murder:create-admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $validator = $this->getContainer()->get('validator');
        $unauthorizedPlayers = $entityManager->getRepository('WinkMurderGameBundle:Player')->findUnauthenticated();

        $output->writeln('Who do you want to become an admin?');
        foreach($unauthorizedPlayers as $index => $player) {
            $output->writeln($index . ':' . $player->getName());
        }

        $dialog = $this->getHelperSet()->get('dialog');

        $selectedPlayer = null;

        $dialog->askAndValidate($output, "<question>Who do you want to become an admin?</question>", function($value) use ($unauthorizedPlayers, &$selectedPlayer) {
            foreach ($unauthorizedPlayers as $index => $player) {
                if (($value == $index) || ($value == $player->getName())) {
                    $selectedPlayer = $player;
                    return;
                }
            }

            throw new \Exception("Please choose a number or enter a name!");
        });


        $account = new AdminAccount($player);

        foreach (array(
            'username' => 'Username',
            'password' => 'Password'
        ) as $property => $label)
        $dialog->askAndValidate($output, "<question>$label:</question>", function($value) use ($validator, $account, $property) {
            $setter = 'set' . ucfirst($property);
            $account->$setter($value);
            if (($errors = $validator->validateProperty($account, $property)) && count($errors)) {
                throw new \Exception($errors[0]->getMessage());
            }
        });

        $entityManager->persist($account);
        $entityManager->flush();
    }

}