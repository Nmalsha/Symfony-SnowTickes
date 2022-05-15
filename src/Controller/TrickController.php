<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Images;
use App\Entity\Trick;
use App\Entity\Videos;
use App\Form\CommentsType;
use App\Form\TrickType;
use App\Form\VideosType;
use App\Repository\CommentsRepository;
use App\Repository\ImagesRepository;
use App\Repository\TrickRepository;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{

    public function __construct(TrickRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/tricks", name="tricks")
     */
    public function tricks(): Response
    {
        return $this->render('blog/tricks.html.twig');
    }
    /**
     * @Route("/trick/{slug}", name="tricks_show", )
     */
    public function readTrick(string $slug, Trick $trick, VideosRepository $videosRepository, ImagesRepository $imagesRepository, TrickRepository $trickRepository, CommentsRepository $commentRepository, Request $request, EntityManagerInterface $manager): Response
    {
        //get trick
        $trick = $trickRepository->findOneBy(['slug' => $slug]);

        //get images

        $images = $imagesRepository->findAll();
        $selImages = [];
        foreach ($images as $image) {

            if ($trick->getId() === $image->getTrick()->getId()) {
                $selImages[] = $image;
            }
        }

        //get videos
        $videos = $videosRepository->findAll();
        $selVideos = [];
        foreach ($videos as $video) {

            if ($trick->getId() === $video->getTrick()->getId()) {
                $selVideos[] = $video;
            }
        }

        //get comments
        $comments = $commentRepository->find(['id' => $trick->getId()]);
        $comments = $commentRepository->findAll();
        $selComments = [];
        foreach ($comments as $comment) {

            if ($trick->getId() === $comment->getTrick()->getId()) {
                $selComments[] = $comment;
            }
        }

        //add comment
        $comments = new Comments;
        $form = $this->createForm(CommentsType::class, $comments);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comments->setCreatedAt(new \DateTimeImmutable());

            // get user
            $user = $this->getUser();

            $comments->setUser($user);
            $comments->setTrick($trick);

            $manager->persist($comments);
            $manager->flush();
            dump('here');

            $this->addFlash(
                'success',
                'Votre commentaire a bien été enregistré !'
            );
            return $this->redirectToRoute('tricks_show', ['slug' => $slug]);
        }

        return $this->render('trick/readTrick.html.twig', [
            'trick' => $trick,
            'selImages' => $selImages,

            'selVideos' => $selVideos,
            'selComments' => $selComments,

            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/tricks/new", name="trick_create")
     */
    function new (Request $request, EntityManagerInterface $manager) {

        $trick = new Trick();
        $img = new Images();
        // $video = new Videos();
        //adding fields to the form
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugname = $form->get("TrickName")->getData();

            $trick->setUser($this->getUser());

            //get Main image data
            $mainimages = $form->get('images')->getData();
            // \dump($mainimage);
            // die;

            foreach ($mainimages as $mainimage) {

                $imageDocument = md5(uniqid()) . '.' . $mainimage->guessExtension();
                // \dump($imageDocument);
                // die;
                //send image name to the images folder
                $mainimage->move(
                    $this->getParameter('images_directory'),
                    $imageDocument
                );
                // save image name to the DB

                //    $img = new Images();

                $img->setName($imageDocument);

                $trick->addImage($img);

                $img->setTrick($trick);

                //  $img->setIsMainImage(1);
                //   $trick->setCreatedOn(new \DateTime());

            }
//get gallery images data
            $gallaryImages = $form->get('gallaryimages')->getData();

            //loop true the images

            foreach ($gallaryImages as $image) {
                $imageDocument = md5(uniqid()) . '.' . $image->guessExtension();

                //lo
                $image->move(
                    $this->getParameter('images_directory'),
                    $imageDocument
                );
                // save image name to the DB

                //  $img = new Images();

                $img->setNameGallaryImages($imageDocument);
                $img->setTrick($trick);
                $trick->addImage($img);
            }

            //if the trick hasn't a id = if the trick already not exist in the DB

            $trick->setslug($slugname);

            $trick->setCreatedOn(new \DateTime());

            //if the form is valid
            $manager->persist($trick);
            $manager->flush();
            //Redirect to the added trick view
            return $this->redirectToRoute('home');

        }

        return $this->render('trick/addTrick.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
        ]);
    }

/**
 * @Route("/tricks/add_video/{id}", name="add_video" ,methods={"POST", "GET"})
 */
    public function addVideos($id, Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $repo->find($id);

        $video = new Videos();

        $form = $this->createForm(VideosType::class);

        $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        $url = $form->get('url')->getData();

        if ($url) {

            $video->setUrl($url);
            //$trick->setVideos($video);
            $video->setTrick($trick);
            // $video->addTrick();
            $manager->persist($video);
            $manager->flush();

            //$trick->setVideos($video);

            // $manager->persist($trick);

            $manager->flush();
            return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()]);

        }
        return $this->render('videos/addVideos.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/tricks/edit/{id}", name="trick_edit" , methods={"POST", "GET"})
     */
    public function edit($id, Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        //video handling

        $repovideos = $this->getDoctrine()->getRepository(Videos::class);
        $videos = $repovideos->findBy(['id' => $id]);

        $img = new Images();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        // $oldImage = $this->getImages($id);
        // \dump($oldImage);
        // die;

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUser($this->getUser());
            // \dump($trick->setUser($this->getUser()));
            // die;

            //get Main image data
            $mainimages = $form->get('images')->getData();

            $mainImageTemp = null;
            foreach ($mainimages as $mainimage) {
                $mainImageTemp = $mainimage;
                // var_dump($mainImageTemp);die();
                $imageDocument = md5(uniqid()) . '.' . $mainimage->guessExtension();
                // \dump($imageDocument);
                // die;
                //send image name to the images folder
                $mainimage->move(
                    $this->getParameter('images_directory'),
                    $imageDocument
                );
                // save image name to the DB

                //    $img = new Images();

                $img->setName($imageDocument);
                $trick->addImage($img);
                //  $img->setIsMainImage(1);
                //   $trick->setCreatedOn(new \DateTime());

            }

            $gallaryImages = $form->get('gallaryimages')->getData();
            //loop true the images

            foreach ($gallaryImages as $image) {
                $imageDocument = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'),
                    $imageDocument
                );
                // save image name to the DB

                //  $img = new Images();

                $img->setNameGallaryImages($imageDocument);
                // \dump($img);
                // die;
                //$img->setName("TOTO");
                $trick->addImage($img);

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
            'videos' => $videos,

        ]);

    }
    /**

     * @Route("/tricks/delete/{id}", name="trick_delete" )
     */
    public function delete($id, Trick $trick, VideosRepository $repoVideo, CommentsRepository $commentRepository, ImagesRepository $repoImage, EntityManagerInterface $manager)
    {

        //delete comment

        $comments = $commentRepository->find(['id' => $trick->getId()]);
        $comments = $commentRepository->findAll();

        foreach ($comments as $comment) {

            if ($trick->getId() === $comment->getTrick()->getId()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($comment);
                $manager->flush();
            }
        }

        //delete videos
        $videos = $repoVideo->findAll();

        foreach ($videos as $video) {

            if ($trick->getId() === $video->getTrick()->getId()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($video);
                $manager->flush();
            }
        }

        //delete images
        $repo = $this->getDoctrine()->getRepository(Images::class);
        $images = $repo->findAll();

        foreach ($images as $image) {
            $imageTrick = $image->getTrick();
            $imageTrickId = $imageTrick->getId();

            $id = (int) $id;
            if ($imageTrickId === $id) {
                $trick->removeImage($image);
                $name = $image->getName();
                if (file_exists($name)) {
                    $imageMain = $this->getParameter('images_directory') . '/' . $name;
                    unlink($imageMain);
                }

                $nameGallery = $image->getNameGallaryImages();
                if (file_exists($nameGallery)) {
                    $imageGallery = $this->getParameter('images_directory') . '/' . $nameGallery;
                    unlink($imageGallery);

                }

                $manager->remove($image);

                $manager->flush();

            }

        }

        $manager->remove($trick);
        $manager->flush();

        $this->addflash(
            'success',
            "Le trick a été supprimé avec succès !"
        );

        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/trick/{id}/comments", name="comment" , methods={"POST", "GET"})
     */
    public function addcomment($id, Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        //display comment form

        $comment = new Comments;

        $form = $this->createForm(CommentsType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setCreatedAt(new \DateTimeImmutable());
            $user = $this->getUser();

            $comment->setUserId($user->getId());

            $manager->persist($comment);

            $manager->flush();

            $comment->addTrick($trick);
            $comment->addUser($this->getUser());

            dump($comment);
            // \dump($user->getId());
            // die;
            // $comment->setUserId();
            $trick->setComments($comment);
            // \dump($comment);
            // die;
            //Redirect to the added trick view
            return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()]);

        }
        return $this->render('comments/addComments.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,

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
     * @Route("/tricks/supprime/galleryimage/{id}", name="delete_gallery_image" , methods={"DELETE"})
     */
    public function deleteGalleryImage(Images $image, Request $request)
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
            $name = $image->getNameGallaryImages();
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
     * @Route("/tricks/supprime/video/{id}", name="delete_video" , methods={"DELETE"})
     */
    public function deleteVideo(Videos $Videos, Request $request)
    {
        $reqData = $request->getContent();

        $data = json_decode($reqData, true);
        error_log("************************************");
        error_log(var_export($data, true));

        error_log($data['_token']);
        error_log($data['_token']);
        //error_log(($request->getContent()));
        //check if the token valid
        if ($this->isCsrfTokenValid('delete' . $Videos->getId(), $data['_token'])) {

            //getting image name from the DB
            $name = $Videos->getUrl();
            //Deleting the image from the directory
            // unlink($this->getParameter('video_directory') . '/' . $name);

            //deleting the image from the DB
            $em = $this->getDoctrine()->getManager();
            $em->remove($Videos);
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
