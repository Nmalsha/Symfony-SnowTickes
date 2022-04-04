<?php

namespace App\Controller;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    // public function index(): Response
    // {
    //     return $this->render('blog/index.html.twig', [
    //         'controller_name' => 'BlogController',
    //     ]);
    // }
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);
        $tricks = $repo->findAll();
        return $this->render('blog/home.html.twig', [
            'controller_name' => "BlogController",
            'tricks' => $tricks,
        ]);
    }
    /**
     * @Route("/tricks", name="tricks")
     */
    public function tricks(): Response
    {
        return $this->render('blog/tricks.html.twig', [
            'title' => "welcome",
            'age' => 31,
        ]);
    }
    /**
     * @Route("/tricks/{id}", name="tricks_show")
     */
    public function read($id): Response
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $repo->find($id);
        return $this->render('blog/read.html.twig', [
            'trick' => $trick,
        ]);
    }
    /**
     * @Route("/signup", name="signup")
     */
    public function signup(): Response
    {
        return $this->render('blog/signup.html.twig', [
            'title' => "signup",
            'age' => 31,
        ]);
    }
    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('blog/login.html.twig', [
            'title' => "welcome",
            'age' => 31,
        ]);
    }
    /**
     * @Route("/trick/new", name="trick_create")
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        if ($request->request->count() > 0) {
            $trick = new Trick();
            $trick->setTrickName($request->request->get('TrickName'))
                ->setDescription($request->request->get('description'))
                ->setCategorie($request->request->get('categorie'))

                ->setCreatedOn(new \DateTime());

            $manager->persist($trick);
            $manager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('blog/createTrick.html.twig');
    }
}
