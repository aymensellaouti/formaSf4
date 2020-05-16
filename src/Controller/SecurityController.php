<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginAuthenticator;
use App\Service\HelperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var HelperService
     */
    private $helper;

    public function __construct(EntityManagerInterface $em, HelperService $helper)
    {
        $this->em = $em;
        $this->helper = $helper;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('formation');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {

    }


    /**
     * @param LoginAuthenticator $loginAuthenticator
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response|null
     * @Route("/register", name="student.register")
     */
    public function registerStudent(LoginAuthenticator $loginAuthenticator,
                              GuardAuthenticatorHandler $guardAuthenticatorHandler,
                              Request $request) {
        if ($this->getUser()) {
            return $this->redirectToRoute('first');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user)
                     ->remove('formateur')
        ;
        $studentForm = $form->get('student');
        $studentForm->remove('formations');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // save user
            $user->setRoles(['ROLE_USER']);
            $this->em->persist($user);
            $this->em->flush();

            //Send welcome email
            $template = $this->renderView('email/welcome.html.twig', ['user' => $user]);
            $this->helper->sendEmail($user->getEmail(), $template);
            // connect user
            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginAuthenticator,
                'main'
            );
        }
        return $this->render('security/subscribe.html.twig', [
           'form' => $form->createView()
        ]);
    }
}
