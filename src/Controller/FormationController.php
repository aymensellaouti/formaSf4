<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Formation::class);
        $formations = $repo->getNotStartedFormationByState(1);
        return $this->render('formation/index.html.twig', [
            'formations' => $formations
        ]);
    }

    /**
     * @Route("/all", name="formation.all")
     */
    public function allFormation()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Formation::class);
        $formations = $repo->findAll();
        return $this->render('formation/index.html.twig', [
            'formations' => $formations
        ]);
    }

    /**
     * @Route("/inactive", name="formation.inactive")
     */
    public function allInactiveFormation()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Formation::class);
        $formations = $repo->findBy(
            array(
                'state'=> 0
            ),
            array(
                'startDate'=>'DESC'
            )
        );
        return $this->render('formation/index.html.twig', [
            'formations' => $formations
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
                     ->remove('students')
        ;
        $form->handleRequest($request);
        //dd($form);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $monImage = $form['imageFile']->getData();
            if ($monImage) {
                $originalFilename = pathinfo($monImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);

                $newFilename = $safeFilename.'-'.uniqid().'.'.$monImage->guessExtension();
                try {
                    $monImage->move(
                        $this->getParameter('formulaire_upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $formation->setImage($newFilename);
                $em = $this->getDoctrine()->getManager();
                $em->persist($formation);
                $em->flush();
            }
            return $this->redirectToRoute('formation');
        }
        return $this->render('formation/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @Route("/update/{id}", name="formation.update")
     */
    public function updateFormation(Request $request, Formation $formation=null) {

        if(!$formation){
            $this->addFlash('error','Formation innexistante');
            return $this->redirectToRoute('formation');
        }

        $form = $this->createForm(FormationType::class, $formation, array(
            'method' => true
        ))
            ->remove('students');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $monImage = $form['imageFile']->getData();
            if ($monImage) {
                $originalFilename = pathinfo($monImage->getClientOriginalName(), PATHINFO_FILENAME);
                dump($originalFilename);
                $newFilename = md5(uniqid()).'.'.$monImage->guessExtension();
                try {
                    $monImage->move(
                        $this->getParameter('formulaire_upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error',"Problème d'upload de votre image");
                    $this->redirectToRoute('formation');
                }
                $formation->setImage($newFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($formation);
            $em->flush();
            return $this->redirectToRoute('formation');
        }
        return $this->render('formation/update.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Formation|null $formation
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/detail/{id}",name="formation.details")
     */
    public function showFormation(Formation $formation = null) {
        if ($formation) {
            return $this->render('formation/formation.html.twig', array(
               'formation' => $formation
            ));
        }
        $this->addFlash('error', 'Formation innexistante :(');
        return $this->redirectToRoute('formation');
    }

    /**
     * @param Formation $formation
     * @Route("/toggle/{id}", name="formation.toggle")
     */
    public function toggleFormation(Formation $formation) {
        if (!$formation) {
            $this->addFlash('error','Formation innexistante');
            return $this->render('formation/formation.html.twig', array(
                'formation' => $formation
            ));
        }
        $formation->setState(!$formation->getState());
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->redirectToRoute('formation');
    }



}
