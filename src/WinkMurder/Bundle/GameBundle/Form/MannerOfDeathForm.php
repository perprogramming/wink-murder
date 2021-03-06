<?php

namespace WinkMurder\Bundle\GameBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MannerOfDeathForm extends AbstractType {

    public function getName() {
        return 'mannerofdeath';
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name_en', 'text', array('label' => 'mannerofdeath.index.form.name_en'));
        $builder->add('name_de', 'text', array('label' => 'mannerofdeath.index.form.name_de'));
    }

}