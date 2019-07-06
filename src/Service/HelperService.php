<?php


namespace App\Service;


use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Twig\Environment;

class HelperService
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

    public function uploadFile(UploadedFile $file, $folderName) {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);

        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        try {
            $file->move(
                $this->uploadDirectory.'/'.$folderName,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        return $newFilename;
    }

    public function generatePdfResponse($html, $fileName) {
        return new PdfResponse(
            $this->pdfGenerator->getOutputFromHtml($html),
            $fileName
            );
    }

    public function sendEmail($to, $template, $contentType='text/html', $attachement = null) {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('aymn.noreply@gmail.com')
            ->setTo($to)
            ->setBody(
                $template,
                $contentType
            );
        if ($attachement) {
            $attachementObject = new \Swift_Attachment($attachement,
                'attachement.pdf',
                'application/pdf' );
            $message->attach($attachementObject);
        }

        $this->mailer->send($message);
    }

}