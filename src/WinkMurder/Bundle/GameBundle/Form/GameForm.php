<?php

namespace WinkMurder\Bundle\GameBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GameForm extends AbstractType {

    public function getName() {
        return 'game';
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('requiredPositiveSuspicionRate', 'integer', array('label' => 'administration.index.currentGame.settings.form.requiredPositiveSuspicionRate'));
        $builder->add('durationOfPreliminaryProceedingsInMinutes', 'integer', array('label' => 'administration.index.currentGame.settings.form.durationOfPreliminaryProceedingsInMinutes'));
        $builder->add('requiredMurders', 'integer', array('label' => 'administration.index.currentGame.settings.form.requiredMurders'));
    }

}