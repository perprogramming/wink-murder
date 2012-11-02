<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use WinkMurder\Bundle\GameBundle\Form\MannerOfDeathForm;
use WinkMurder\Bundle\GameBundle\Entity\MannerOfDeath;

/**
 * @Route("/administration/manner-of-death")
 */
class MannerOfDeathController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array(
            'mannersOfDeath' => $this->getMannerOfDeathRepository()->findAll()
        );
    }

    /**
     * @Route("/new")
     * @Template
     */
    public function newAction() {
        return array('form' => $this->createForm(new MannerOfDeathForm())->createView());
    }

    /**
     * @Route("/create")
     * @Method("POST")
     */
    public function createAction(Request $request) {
        $form = $this->createForm(new MannerOfDeathForm());
        $form->bind($request);

        $data = $form->getData();
        $entityManager = $this->getEntityManager();

        $manner = new MannerOfDeath();
        $entityManager->persist($manner);

        $manner->setTranslatableLocale('en');
        $manner->setName($data['name_en']);
        $entityManager->flush();

        $manner->setTranslatableLocale('de');
        $manner->setName($data['name_de']);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('winkmurder_game_mannerofdeath_index'));
    }

    /**
     * @Route("/edit/{id}")
     * @Template
     */
    public function editAction($id) {
        $manner = $this->getMannerOfDeathRepository()->find($id);
        $entityManager = $this->getEntityManager();
        $data = array();

        $manner->setTranslatableLocale('en');
        $entityManager->refresh($manner);
        $data['name_en'] = $manner->getName();

        $manner->setTranslatableLocale('de');
        $entityManager->refresh($manner);
        $data['name_de'] = $manner->getName();

        $form = $this->createForm(new MannerOfDeathForm(), $data);
        return array(
            'manner' => $manner,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/update/{id}")
     * @Method("POST")
     */
    public function updateAction($id, Request $request) {
        $form = $this->createForm(new MannerOfDeathForm());
        $form->bind($request);

        $manner = $this->getMannerOfDeathRepository()->find($id);
        $data = $form->getData();
        $entityManager = $this->getEntityManager();

        $manner->setTranslatableLocale('en');
        $manner->setName($data['name_en']);
        $entityManager->flush();

        $manner->setTranslatableLocale('de');
        $manner->setName($data['name_de']);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('winkmurder_game_mannerofdeath_index'));
    }

    /**
     * @Route("/delete/{id}")
     * @Method("POST")
     */
    public function deleteAction($id) {
        $manner = $this->getMannerOfDeathRepository()->find($id);
        $entityManager = $this->getEntityManager();
        $entityManager->remove($manner);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_mannerofdeath_index'));
    }

}