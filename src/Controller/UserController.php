<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\MailServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        MailServiceInterface $mailService): Response {
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

            $user->setActivationToken(md5(uniqid()));

            $entityManager->persist($user);
            $entityManager->flush();

            $mailService->send(
                $user->getEmail(),
                'Activate your account',
                'reset_password/activation.html.twig',
                ['token' => $user->getActivationToken()]
            );

            return $this->redirectToRoute('activation_message');
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

    /**
     * @Route("/userProfile/{id}", name="user_profile")
     */
    public function userProfile($id, User $user, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);

        $form = $this->createForm(UserProfileType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//get data from the form
            $profileImages = $form->get('profieImage')->getData();
            foreach ($profileImages as $profileImage) {
                // \dump($profileImage.originalName);
                // die;
                $imageDocument = md5(uniqid()) . '.' . $profileImage->guessExtension();

                $profileImage->move(
                    $this->getParameter('images_directory'),
                    $imageDocument
                );
                $user->setprofieImage($imageDocument);
                $entityManager->persist($user);
            }

            $entityManager->flush();
            return $this->redirectToRoute('user_profile', ['id' => $id]);
        }
        return $this->render('user/userProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

}
