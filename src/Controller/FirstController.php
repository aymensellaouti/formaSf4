<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    /**
     * @Route("/", name="first")
     */
    public function index()
    {
        return $this->render('first/index.html.twig', [
            'controller_name' => 'FirstController',
        ]);
    }

    public function header() {
       return $this->render('header.html.twig');
    }

    /**
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/testmail/{id}",name="mail.test")
     */
    public function mail(Formation $formation, HelperService $helperService)
    {
        $htmlView = $this->render('formation/formation_fiche.html.twig',array(
            'formation'=>$formation
        ));
        $fileName = $formation->getDesignation().'.pdf';
        $mail = $this->renderView('email/test.html.twig');
        $pdf = $helperService->generatePdfResponse($htmlView, $fileName);
        $helperService->sendEmail('aymen.sellaouti@gmail.com',$mail, 'text/html', $pdf);
        return $this->render('email/test.html.twig');
    }
}
