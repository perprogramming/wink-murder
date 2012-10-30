<?php

namespace WinkMurder\Bundle\GameBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TranslationsDiffCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('wink-murder:translations:diff');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $outputEnglish = '';
        $inputFileEnglish = __DIR__ . '/../Resources/translations/messages.en.yml';
        $outputFileEnglish = $this->getContainer()->getParameter('project.tempdir') . '/messages_english';
        $english = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($inputFileEnglish));
        $this->dumpArrayKeys($english, $outputEnglish);
        file_put_contents($outputFileEnglish, $outputEnglish);

        $outputGerman = '';
        $inputFileGerman = __DIR__ . '/../Resources/translations/messages.de.yml';
        $outputFileGerman = $this->getContainer()->getParameter('project.tempdir') . '/messages_german';
        $german = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($inputFileGerman));
        $this->dumpArrayKeys($german, $outputGerman);
        file_put_contents($outputFileGerman, $outputGerman);

        $diff = array();
        exec("diff -u $outputFileGerman $outputFileEnglish", $diff);
        unlink($outputFileEnglish);
        unlink($outputFileGerman);

        foreach ($diff as $line) {
            $output->writeln($line);
        }
    }

    protected function dumpArrayKeys(array $array, &$output, $stack = array()) {
        foreach ($array as $key => $value) {
            array_push($stack, $key);
            if (is_array($value)) {
                $this->dumpArrayKeys($value, $output, $stack);
            } else {
                $output .= implode('.', $stack) . "\n";
            }
            array_pop($stack);
        }
    }

}