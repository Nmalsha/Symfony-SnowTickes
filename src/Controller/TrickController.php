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
     */
    function new (Request $request, EntityManagerInterface $manager) {

        $trick = new Trick();

        //adding fields to the form
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setUser($this->getUser());
            // \dump($userId);
            // die;
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

            $trick->setCreatedOn(new \DateTime());

            //if the form is valid
            $manager->persist($trick);
            $manager->flush();
            //Redirect to the added trick view
            return $this->redirectToRoute('home');

        }

        return $this->render('trick/addTrick.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/tricks/edit/{id}", name="trick_edit" , methods={"POST", "GET"})
     */
    public function edit($id, Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        // $oldImage = $this->getImages($id);
        // \dump($oldImage);
        // die;
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUser($this->getUser());
            // \dump($userId);
            // die;
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

                // $trick->setCreatedOn(new \DateTime());

            }

            //if the trick hasn't a id = if the trick already not exist in the DB

            $trick->setCreatedOn(new \DateTime());

            //if the form is valid
            $manager->persist($trick);
            $manager->flush();
            //Redirect to the added trick view
            return $this->redirectToRoute('home');

        }

        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
        ]);

    }

    /**
     * @Route("/tricks/supprime/mainimage/{id}", name="delete_main_image" , methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $request)
    {
        $reqData = $request->getContent();

        $data = json_decode($reqData, true);
        error_log("************************************");
        error_log(var_export($data, true));

        error_log($data['_token']);
        error_log($data['_token']);
        //error_log(($request->getContent()));
        //check if the token valid
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {

            //getting image name from the DB
            $name = $image->getName();
            //Deleting the image from the directory
            unlink($this->getParameter('images_directory') . '/' . $name);
            //deleting the image from the DB
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            //return new JsonReponse(['success' => 1]);
            error_log("******************TOKEN OK******************");
            return new Response("OKy");
        } else {
            // return new JsonReponse(['error' => 'Invalide token'], 400);

            error_log("******************TOKEN ERR******************");
            return new Response("KOy");
        }
    }

    /**

     * @Route("/trick/{id}/gallery", name="trick_gallery")
     */
//     public function addGallaryImage($id, Trick $trick = null, GallaryImage $GallaryImage = null, Request $request, EntityManagerInterface $manager)
    //     {
    //         $repo = $this->getDoctrine()->getRepository(Trick::class);
    //         $trick = $repo->find($id);

//         $form = $this->createForm(GallaryImageType::class);

//         $form->handleRequest($request);
    //         if ($form->isSubmitted() && $form->isValid()) {

//             $trickId = $request->get('trickId');
    //             $galarieimages = $form->get('galarieimages')->getData();

//             // dd($galarieimages);
    //             // die;
    //             foreach ($galarieimages as $galarieimage) {

//                 $imageDocument = md5(uniqid()) . '.' . $galarieimage->guessExtension();
    // //send image name to the gallaryImage folder
    //                 $galarieimage->move(
    //                     $this->getParameter('galarie_images_directory'),
    //                     $imageDocument
    //                 );

//                 // save image name to the DB

//                 $img = new GallaryImage();

//                 $img->setName($imageDocument);
    //                 $trick->addGallaryImage($img);
    //                 // // $trick->setTrickId($trickId);
    //                 // // // $trick->setCreatedOn(new \DateTime());

//             }

//             //if the form is valid
    //             $manager->persist($trick);
    //             $manager->flush();
    //             //Redirect to the added trick view
    //             return $this->redirectToRoute('home');
    //         }

//         return $this->render('GallaryImage/addGallaryImage.php', [
    //             'formGallary' => $form->createView(),
    //             'trick' => $trick,
    //             'id' => $trick->getId() !== null,
    //         ]);
    //     }

}
