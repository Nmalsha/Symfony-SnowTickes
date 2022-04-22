<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\MailServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
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
            // \dump($user->getResetToken());
            // die;
            // $url = $this->generateUrl('app_reset_password', ['resetToken' => $resetToken],
            //     UrlGeneratorInterface::ABSOLUTE_URL
            // );
            // \dump($url);
            // die;
            //  dd($user);
            $mailService->send(
                $user->getEmail(),
                'RESET YOUR Password',
                'reset_password/email.html.twig',
                [
                    'token' => $user->getResetToken(),
                    'url' => $this->generateUrl('app_reset_password', ['token' => $resetToken],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ]
            );

            // $email = (new Email())
            //     ->from('sudani.malsha@gmail.com')
            //     ->to($user->getEmail())
            //     ->subject('RESET Password')
            //     ->html($this->renderView('reset_password/check_email.html.twig', [
            //         'token' => $user->getResetToken(),
            //     ]), 'text/html')
            // ;
            // $sucess = $mailer->send($email);

            return $this->redirectToRoute('app_check_email');
        }

        // return $this->processSendingPasswordResetEmail(
        //     $form->get('email')->getData(),
        //     $mailer,
        //     $translator
        // );

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
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, TranslatorInterface $translator, string $token = null): Response
    {
//getting the reset token from the database
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);
//check if a user exist with that token
        if (!$user) {
            //if not exist
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('app_login');
        }
//if exist
        if ($request - isMethod('POST')) {
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('new-password')));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Password changed succz');
        } else {
            return $this->render('reset_password/request.html.twig', ['token' => $token]);
        }
        // if ($token) {
        //     // We store the token in session and remove it from the URL, to avoid the URL being
        //     // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
        //     $this->storeTokenInSession($token);

        //     return $this->redirectToRoute('app_reset_password');
        // }

        // $token = $this->getTokenFromSession();
        // if (null === $token) {
        //     throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        // }

        // try {
        //     $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        // } catch (ResetPasswordExceptionInterface $e) {
        //     $this->addFlash('reset_password_error', sprintf(
        //         '%s - %s',
        //         $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
        //         $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
        //     ));

        //     return $this->redirectToRoute('app_forgot_password_request');
        // }

        // // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     // A password reset token should be used only once, remove it.
        //     $this->resetPasswordHelper->removeResetRequest($token);

        //     // Encode(hash) the plain password, and set it.
        //     $encodedPassword = $userPasswordHasher->hashPassword(
        //         $user,
        //         $form->get('plainPassword')->getData()
        //     );

        //     $user->setPassword($encodedPassword);
        //     $this->entityManager->flush();

        //     // The session is cleaned up after the password has been changed.
        //     $this->cleanSessionAfterReset();

        //     return $this->redirectToRoute('home');
        // }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    public function processSendingPasswordResetEmail(
        string $emailFormData,
        MailServiceInterface $mailService,
        TranslatorInterface $translator): RedirectResponse {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
            //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            // ));

            return $this->redirectToRoute('app_check_email');
        }

        $mailService->send(
            $user->getEmail(),
            'Your password reset request',
            'reset_password/email.html.twig',
            ['resetToken' => $resetToken]
        );

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
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
