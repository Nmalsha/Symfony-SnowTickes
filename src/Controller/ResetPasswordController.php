<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\MailServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;
    private $entityManager;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, EntityManagerInterface $entityManager, UserRepository $UserRepository, MailServiceInterface $mailService)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("/resetpass", name="app_forgot_password_request")
     */
    public function request(Request $request, MailServiceInterface $mailService,
        TranslatorInterface $translator, UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $user = $userRepository->findOneByEmail($data['email']);

            if (!$user) {
                $this->addFlash('danger', 'Cet address n\'existe pas');
                return $this->redirectToRoute('app_forgot_password_request');
            }
            $resetToken = $tokenGenerator->generateToken();

            $user->setResetToken($resetToken);

            $entityManager->persist($user);
            $entityManager->flush();

            $mailService->send(
                $user->getEmail(),
                'RESET YOUR Password',
                'reset_password/email.html.twig',
                [
                    'token' => $user->getResetToken(),
                    'url' => $this->generateUrl('app_reset_password', ['token' => $user->getResetToken()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ]
            );

            return $this->redirectToRoute('app_check_email');
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password")
     */
    public function reset(Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        $token): Response {

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

//getting the reset token from the database
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);
//check if a user exist with that token
        if (!$user) {
            //if not exist
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('app_login');
        }
//if exist
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setResetToken(null);
            $planePassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $planePassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
            'token' => $token,
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/message", name="activation_message")
     */
    public function message(): Response
    {
        return $this->render('reset_password/message.html.twig');
    }
    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/mailsent", name="mailsent")
     */
    public function sendMail(): Response
    {
        return $this->render('reset_password/send_email.html.twig');
    }

}
