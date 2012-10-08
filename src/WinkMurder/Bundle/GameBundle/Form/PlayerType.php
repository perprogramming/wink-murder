<?php

namespace WinkMurder\Bundle\GameBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilder;

class PlayerType extends AbstractType {

    protected $roleLabels;

    public function __construct(array $roleLabels) {
        $this->roleLabels = $roleLabels;
    }

    public function getName() {
        return 'winkmurder_game_playertype';
    }

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
            ->add('name', 'text', array('label' => 'Name'))
            ->add('username', 'text', array('label' => 'Benutzername'))
            ->add('password', 'text', array('label' => 'Passwort'))
            ->add('avatarFile', 'file', array('label' => 'Avatar', 'required' => false))
            ->add('roles', 'choice', array(
                'label' => 'Rollen',
                'choices' => $this->roleLabels,
                'expanded' => true,
                'required' => true,
                'multiple' => true
            ))
        ;
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'WinkMurder\Bundle\GameBundle\Entity\Player'
        );
    }

}