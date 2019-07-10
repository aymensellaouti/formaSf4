<?php


namespace App\Service;


use Knp\Snappy\Pdf;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Twig\Environment;

class EmailService
{
    private $uploadDirectory;
    /**
     * @var Pdf
     */
    private $pdfGenerator;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var TwigExtension
     */
    private $twig;

    public function __construct(
        $uploadDirectory,
        Pdf $pdfGenerator,
        \Swift_Mailer $mailer,
        Environment $twig
    )
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->pdfGenerator = $pdfGenerator;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function welcome() {

    }

}