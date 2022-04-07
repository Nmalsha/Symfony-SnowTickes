<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
        $repoImage = $this->getDoctrine()->getRepository(Images::class);
        $Images = $repoImage->findAll();
        return $this->render('blog/home.html.twig', [
            'controller_name' => "BlogController",
            'tricks' => $tricks,
            'images' => $Images,
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
     * @Route("/trick/{id}", name="tricks_show")
     */
    public function read(int $id, Request $request): Response
    {

        $repo = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $repo->find($id);

        $repoImage = $this->getDoctrine()->getRepository(Images::class);

        $images = $repoImage->findAll();

        foreach ($images as $image) {

            //get the trick from images repo

            $imagesTrick = $image->getTrick();
            // get trick id from images
            $imagerepoTrickId = $imagesTrick->getId();
            if ($id === $imagerepoTrickId) {
                $imagename = $image->getName();

            }

        }

        // \dump($imagename);

        return $this->render('blog/read.html.twig', [
            'trick' => $trick,
            'imagename' => $imagename,
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
     * @Route("/tricks/new", name="trick_create")
     * @Route("/trick/{id}/edit", name="trick_edit")
     */
    public function form(Trick $trick = null, Images $images = null, Request $request, EntityManagerInterface $manager)
    {

        if (!$trick) {
            $trick = new Trick();
        }

        //adding fields to the form
        $form = $this->createFormBuilder($trick)
            ->add('TrickName')
            ->add('description')
            ->add('categorie')
            ->add('images', FileType::class, [
                'multiple' => false,

                'label' => false,
                'mapped' => false,
                'required' => false,
            ])

            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //get image data
            $images = $form->get('images')->getData();

            //loop true the images
            foreach ($images as $image) {

                $imageDocument = md5(uniqid()) . '.' . $image->guessExtension();
//send image name to the images folder
                $image->move(
                    $this->getParameter('images_directory'),
                    $imageDocument
                );
// save image name to the DB

                $img = new Images();

                $img->setName($imageDocument);
                $trick->addImage($img);
                $trick->setCreatedOn(new \DateTime());

            }

            //if the trick hasn't a id = if the trick already not exist in the DB
            if (!$trick->getId()) {
                $trick->setCreatedOn(new \DateTime());
            }

            //if the form is valid
            $manager->persist($trick);
            $manager->flush();
            //Redirect to the added trick view
            return $this->redirectToRoute('tricks_show', ['id' => $trick->getId()]);

        }

        return $this->render('blog/createTrick.html.twig', [
            'formTrick' => $form->createView(),
            'trick' => $trick,
            'editMode' => $trick->getId() !== null,
        ]);
    }

    /**

     * @Route("/trick/addGalarie/{id}", name="trick_galarie")
     */
    public function addGalarieImage(Request $request, EntityManagerInterface $manager)
    {

    }

// /**

// //  * @Route("/supprime/trick/{id}", name="trick_edit" metthods=)
    // //  */
    //     // public function deleteImage(Trick $trick = null, Request $request, EntityManagerInterface $manager)
    //     // {

    //  }

}
