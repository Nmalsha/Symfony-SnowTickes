<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Trick;
use App\Entity\User;
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
        $repo = $this->getDoctrine()->getRepository(Trick::class);
        $tricks = $repo->findAll();
        $repoImage = $this->getDoctrine()->getRepository(Images::class);
        $Images = $repoImage->findAll();
        $repoUser = $this->getDoctrine()->getRepository(User::class);
        $user = $repoUser->findAll();
        return $this->render('blog/home.html.twig', [
            'controller_name' => "BlogController",
            'tricks' => $tricks,
            'images' => $Images,
            'user' => $user,
        ]);
    }
    /**
     * @Route("/tricks", name="tricks")
     */
    // public function tricks(): Response
    // {
    //     return $this->render('blog/tricks.html.twig');
    // }

    /**
     * @Route("/login", name="login")
     */
    // public function login(): Response
    // {
    //     return $this->render('blog/login.html.twig', [
    //         'title' => "welcome",
    //         'age' => 31,
    //     ]);
    // }

    /**

     * @Route("/trick/addGalarie/{id}", name="trick_galarie")
     */
    // public function addGalarieImage(Request $request, EntityManagerInterface $manager)
    // {

    // }

    /**

     * @Route("/supprime/trick/{id}", name="trick_delete_image" method={"DELETE"})
     */

    // public function deleteImage(Images $image, Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);
    //     //if token valide
    //     if ($this->isCsrfTokenValid('delete' . $image - getId(), $data['_token'])) {
    //         //getting the name of the image
    //         $nom = $image->getName();
    //         //delete image
    //         unlink($this->getParameter('images_directory') . '/' . $nom);
    //         //delete from DB
    //         $entitymanage = $this - getDoctrine()->getRepository(Images::class);
    //         $entitymanage->remove($image);
    //         $entitymanage->flush();

    //         return new jsonReponse(['success' => 1]);
    //     } else {
    //         return new jsonReponse(['error' => 'Token invalide'], 400);
    //     }

    // }

}
