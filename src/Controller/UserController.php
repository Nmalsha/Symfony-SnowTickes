<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\UserAuthenticator;
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
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
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
// genarate activation token

            $user->setActivationToken(md5(uniqid()));

            $entityManager->persist($user);
            $entityManager->flush();

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

    public function activation($token, UserRepository $userRepository)
    {
//check if there is a user with this token
        $user = $userRepository->findOneBy(['activation_token' => $token]);
        //if nobody with this token

        if (!$user) {
// Error 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
    }
}
