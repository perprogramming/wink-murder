<?php

namespace WinkMurder\Bundle\GameBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MannerOfDeathForm extends AbstractType {

    public function getName() {
        return 'mannerofdeath';
    }

    public function buildForm(FormBuilder $builder, array $options) {
        $builder->add('name_en', 'text', array('label' => 'mannerofdeath.index.form.name_en'));
        $builder->add('briefing_en', 'textarea', array('label' => 'mannerofdeath.index.form.briefing_en'));
        $builder->add('name_de', 'text', array('label' => 'mannerofdeath.index.form.name_de'));
        $builder->add('briefing_de', 'textarea', array('label' => 'mannerofdeath.index.form.briefing_de'));
    }

}