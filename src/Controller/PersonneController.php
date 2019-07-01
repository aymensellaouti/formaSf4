<?php

namespace App\Controller;

use App\Entity\Personne;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PersonneController
 * @package App\Controller
 * @Route("/personne")
 */
class PersonneController extends AbstractController
{
    /**
     * @Route("/", name="personne", name="add.personne")
     */
    public function index()
    {
        return $this->render('personne/index.html.twig', [
            'controller_name' => 'PersonneController',
        ]);
    }

    /**
     * @param Personne|null $personne
     * @Route("/delete/{id}", name="delete.personne")
     */
    public function deletePersonne(Personne $personne=null) {
        if($personne){
            $em = $this->getDoctrine()->getManager();
            $em->remove($personne);
            $em->flush();
            $this->addFlash('success',"personne supprimée avec succès");
        } else {
            $this->addFlash('error', 'personne innexistante');
        }

        return $this->render('personne/liste.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/list", name="list.personne")
     */
    public function listPersonne() {
        $repository = $this->getDoctrine()->getRepository(Personne::class);
        $personnes = $repository->findAll();

        return $this->render('',array(
           'personnes' => $personnes
        ));
    }

    /**
     * @param $name
     * @param $firstname
     * @param $age
     * @param $cin
     * @param $path
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/add/{name}/{firstname}/{age}/{cin}/{path}")
     */
    public function addPersonne($name, $firstname, $age, $cin, $path) {
        $personne = new Personne();
        $personne->setName($name);
        $personne->setFirstname($firstname);
        $personne->setAge($age);
        $personne->setCin($cin);
        $personne->setPath($path);

        $em = $this->getDoctrine()->getManager();
        $em->persist($personne);
        $em->flush();
        return $this->render('personne/index.html.twig', array(
            'personne' => $personne
        ));
    }
}
