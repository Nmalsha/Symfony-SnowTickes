<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/signup/user", name="signup")
     *
     */
    public function formsignup(UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $manager)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            //if the form is valid
            $manager->persist($user);
            $manager->flush();
            //Redirect to the added trick view
            return $this->redirectToRoute('home');
        }

        // $formsignup = $this->createFormBuilder($user)
        //     ->add('TrickName')
        //     ->add('description')
        //     ->add('categorie')
        //     ->getForm();

        return $this->render('user/signup.html.twig', [
            'formuser' => $form->createView(),
            'user' => $user,
        ]);
    }
}
