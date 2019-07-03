<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FormationController
 * @package App\Controller
 * @Route("/formation")
 */
class FormationController extends AbstractController
{
    /**
     * @Route("/", name="formation")
     */
    public function index()
    {
        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }

    /**
     * @param Request $request
     * @Route("/add", name="formation.add")
     */
    public function addFormation(Request $request) {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation)
                     ->remove('state')
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            // Todo add formation
            return $this->redirectToRoute('formation');
        }
        return $this->render('formation/add.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
