<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('blog/home.html.twig', [
            'title' => "welcome",
            'age' => 31,
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
     * @Route("/tricks/10", name="tricks_show")
     */
    public function read(): Response
    {
        return $this->render('blog/read.html.twig', [
            'title' => "welcome",
            'age' => 31,
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
}
