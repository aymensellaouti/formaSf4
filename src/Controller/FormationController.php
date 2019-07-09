<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Formation;
use App\Entity\User;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Service\HelperService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FormationController
 * @package App\Controller
 * @Route("/formation")
 */
class FormationController extends AbstractController
{

    private $helperService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(HelperService $helperService, EntityManagerInterface $entityManager)
    {
        $this->helperService = $helperService;
        $this->entityManager = $entityManager;
    }

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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
                $newFilename = $this->helperService->uploadFile($monImage, 'formations');
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
     * @Route("/detail/{id}/{fiche?0}",name="formation.details")
     */
    public function showFormation(Formation $formation = null, $fiche) {
        if ($formation) {
            $fiche?$view = 'formation/formation_fiche.html.twig':$view = 'formation/formation.html.twig';
            return $this->render($view, array(
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

    /**
     * @param Formation $formation
     * @return \Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse
     * @Route("/pdf/{id}", name="formation.fiche.pdf")
     */
    public function pdfFormationFiche(Formation $formation) {
        $htmlView = $this->render('formation/formation_fiche.html.twig',array(
            'formation'=>$formation
        ));
        $fileName = $formation->getDesignation().'.pdf';
        $pdf = $this->helperService->generatePdf($htmlView, $fileName);
        return new Response('<html><body>Pdf generated with success</body></html>');
    }

    /**
     * @Route("/evaluate/{id}", name="formation.evaluate")
     */
    public function wishlist(Request $request, Formation $formation=null) {
        /** @var User $user */
        $user = $this->getUser();
        $description = $request->get('description');
        $note = $request->get('note');

        if($note > 5 || $note<1) {
            $content = json_encode('La note doit etre entre 1 et 5');
            return new Response($content, 401);
        }
        if(!$formation || !$user) {
            $content = json_encode('You are not allowed to delete this post');
            return new Response($content, 401);
        }
        else {
            if(! in_array('ROLE_STUDENT', $user->getRoles())) {
                $content = json_encode('only students can evaluate trainings');
                return new Response($content, 401);
            }
        }
        $evaluation = new Evaluation();
        $evaluation->setDate(new \DateTime());
        $evaluation->setDescription($description);
        $evaluation->setFormation($formation);
        $evaluation->setUser($user);
        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();
        $datas = [];
        foreach ($formation->getEvaluations() as $evaluationItem) {
            $row = [
                'date' => $evaluationItem->getDate()->format('Y-m-d'),
                'username' => $evaluationItem->getUser()->getUsername(),
                'description' => $evaluationItem->getDescription()
            ];
            $datas[] = $row;
        }
        return $this->json($datas);
    }

}
