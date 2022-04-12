<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Trick;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/tricks", name="tricks")
     */
    public function tricks(): Response
    {
        return $this->render('blog/tricks.html.twig');
    }
    /**
     * @Route("/trick/{id}", name="tricks_show")
     */
    public function readTrick(int $id, Request $request): Response
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

        return $this->render('trick/readTrick.html.twig', [
            'trick' => $trick,
            'imagename' => $imagename,
        ]);
    }

    /**
     * @Route("/tricks/new", name="trick_create")
     * @Route("/trick/{id}/edit", name="trick_edit")
     */
    public function addTrick(Trick $trick = null, Images $images = null, Request $request, EntityManagerInterface $manager)
    {

        if (!$trick) {
            $trick = new Trick();
        }

        //adding fields to the form
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userId = $request->get('userId');
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
                $trick->setUserId($userId);
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
            return $this->redirectToRoute('home');

        }

        return $this->render('trick/addTrick.html.twig', [
            'formTrick' => $form->createView(),
            'trick' => $trick,
            'editMode' => $trick->getId() !== null,
        ]);
    }

}
