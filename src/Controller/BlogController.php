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
    public function read($id): Response
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $repo->find($id);
        $repoImage = $this->getDoctrine()->getRepository(Images::class);

        // $Image = $repoImage->getImages($id);

        return $this->render('blog/read.html.twig', [
            'trick' => $trick,
            // 'image' => $Image,
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
    public function form(Trick $trick = null, Request $request, EntityManagerInterface $manager)
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
                'multiple' => true,
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
            'editMode' => $trick->getId() !== null,
        ]);
    }

}
