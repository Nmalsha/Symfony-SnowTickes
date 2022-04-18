<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //encode the plain password
            $plaintextPassword = $user->getPassword();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $plaintextPassword
                )
            );
            //genarate activation token

            $user->setActivationToken(md5(uniqid()));

            $entityManager->persist($user);
            $entityManager->flush();
            // Create message

            $email = (new \Swift_Message('Activate your account'))
                ->setFrom('sudani.malsha@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'reset_password/activation.html.twig',
                        ['token' => $user->getActivationToken()]
                    ),
                    'text/html'
                )
            ;

            // $email = (new TemplatedEmail())
            //     ->from(new Address('sudani.malsha@gmail.com', 'SnowTrick Activattion de votre compte'))
            //     ->to($user->getEmail())
            //     ->subject('Activate your account')
            //     ->htmlTemplate('reset_password/actovation.html.twig')
            //     // ->context($this->renderView('reset_password/actovation.html.twig', [

            //     //     'token' => $user->getActivationToken(),
            //     // ])
            //     // )
            //     ->context([

            //         'token' => $user->getActivationToken(),
            //     ])

            //     //  ->body($this->renderView('reset_password/activation.html.twig', ['token' => $user->getActivationToken()]),

            //     //     'text/html'
            //     // )
            // ;

            // $email = (new Email())
            //     ->from('sudani.malsha@gmail.com')
            //     ->to(new Address($user->getEmail()))

            //     ->subject('Activate your account')
            //     ->htmlTemplate('reset_password/actovation.html.twig')
            //     ->context([
            //         'token' => $user->getActivationToken(),
            //     ])
            // ;
            //      $message = (new Email())
            //Define the sender
            // ->from('hello@example.com')
            //     ->to('you@example.com')
            // //->cc('cc@example.com')
            // //->bcc('bcc@example.com')
            // //->replyTo('fabien@example.com')
            // //->priority(Email::PRIORITY_HIGH)
            //     ->subject('Time for Symfony Mailer!')
            //     ->text('Sending emails is fun again!')
            //     ->html('<p>See Twig integration for better HTML integration!</p>');
            //    ->from('malshis@yahoo.com')
            //Define the destination
            // ->to($user->getEmail())
            // ->priority(Email::PRIORITY_HIGH)
            // ->subject('Time for Symfony Mailer!')
            // ->text('Sending emails is fun again!')
            // ->html('<p>See Twig integration for better HTML integration!</p>');
            // ->setBody(
            //     $this->renderView(
            //         'emails/activation.html.twig', ['token' => $user->getActivationToken()]
            //     ),

            // );
            // send mail
            $mailer->send($email);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/activation/{token}", name="activation")
     */

    public function activation($token, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
//check if there is a user with this token
        $user = $userRepository->findOneBy(['activation_token' => $token]);
        //if nobody with this token

        if (!$user) {
// Error 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
        //delete token
        $user->setActivationToken(null);
        $entityManager->persist($user);
        $entityManager->flush();
//Send a flash message
        $this->addFlash('message', 'Vous avez bien activé votre compte');
//redirect after the activation

        return $this->redirectToRoute('home');

    }
}
