<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Images;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Videos;
use App\Form\CommentsType;
use App\Form\TrickType;
use App\Form\VideosType;
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
    public function readTrick(int $id, Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        // Add comment
        $comments = new Comments;
        // create form
        $form = $this->createForm(CommentsType::class, $comments);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comments->setCreatedAt(new \DateTimeImmutable());
            //get user
            $user = $this->getUser();
            //add user id to the comment
            $comments->setUserId($user->getId());

            $comments->setTrickId($id);
            //send comment to the DB

            $manager->persist($comments);
            $manager->flush();

            // $comments->addTrick($trick);

            // $manager->persist($comment);

            // $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été enregistré !'
            );
            //redirect root
            return $this->redirectToRoute('tricks_show', ['id' => $trick->getId()]);
            // $comment->addTrick($trick);
            // $comment->addUser($this->getUser());

        }
        // $comments->addTrick($trick);
        // $comments->addUser($this->getUser());
        // \dump($comments);
        // die;
        //display video,image and comment repositorys
        $repo = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $repo->find($id);

        $repoImage = $this->getDoctrine()->getRepository(Images::class);
        $repovideos = $this->getDoctrine()->getRepository(Videos::class);
        $repoComments = $this->getDoctrine()->getRepository(Comments::class);

        //get images
        $images = $repoImage->findAll();

        $selImages = [];
        foreach ($images as $image) {

            //get the trick from images repo

            $imagesTrick = $image->getTrick();

            // get trick id from images
            $imagerepoTrickId = $imagesTrick->getId();

            //if trick id and the imagetrick id is same
            if ($id === $imagerepoTrickId) {
                $selImages[] = $image;

                $imagename = $image->getName();
                $galaryImageNames = $image->getNameGallaryImages();

            }

        }
        //get videos
        $videos = $repovideos->findAll();

        $selVideos = [];
        foreach ($videos as $video) {

            //   $videoTrick = $video->getTrick();
            //get the trickid from images repo
            $videorepoTrickId = $video->getTrickId();

            if ($id === $videorepoTrickId) {
                $selVideos[] = $video;
                // \dump($videorepoTrickId);
                // dump($id);
                // die;
                // \dump($video);
                // die;
                $videoname = $video->getUrl();

            }

        }
//getting comment owner
        // $repouserOfTheComment = $this->getDoctrine()->getRepository(User::class);
        // $users = $repouserOfTheComment->findAll();
        // // $userofthecomment = $user->findBy($commentrepoUserid);
        // foreach ($users as $user) {
        //     $commentrepoUserid = $commnent->getUserId();

        //     if(){

        //     }
        // }
        $commnents = $repoComments->findAll();

        $selComments = [];

        $repoUser = $this->getDoctrine()->getRepository(User::class);

        //get images
        $images = $repoImage->findAll();

        foreach ($commnents as $commnent) {

            //   $videoTrick = $video->getTrick();

            $commentrepoTrickid = $commnent->getTrickId();

            // \dump($commentrepoTrickid);
            // die;
            if ($id === $commentrepoTrickid) {

                //$content = $commnent->getContent();
                // \dump($videorepoTrickId);

                //dump($repoUser->findAll());
                $commnent->userObj = $repoUser->findOneBy(['id' => $commnent->getUserId()]);

                // dump($commnent);
                // die;

                $selComments[] = $commnent;
                // $videoname = $video->getUrl();

            }

        }

        // dump($selComments);
        // die;
        return $this->render('trick/readTrick.html.twig', [
            'trick' => $trick,

            'selVideos' => $selVideos,
            'selImages' => $selImages,
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

            $trick->setUser($this->getUser());
            // \dump($userId);
            // die;

            // \dump($url);
            // die;
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
                //  $img->setIsMainImage(1);
                //   $trick->setCreatedOn(new \DateTime());

            }
//get gallery images data
            $gallaryImages = $form->get('gallaryimages')->getData();
            \dump($gallaryImages);

            //loop true the images

            foreach ($gallaryImages as $image) {
                $imageDocument = md5(uniqid()) . '.' . $image->guessExtension();
                \dump($image);

                //lo
                $image->move(
                    $this->getParameter('images_directory'),
                    $imageDocument
                );
                // save image name to the DB

                //  $img = new Images();

                $img->setNameGallaryImages($imageDocument);

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

        return $this->render('trick/addTrick.html.twig', [
            'form' => $form->createView(),
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
            $video->setTrickId($trick->getId());

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

        //$userID =

        //var_dump(array   $GLOBALS['app']);
        //die("tedt");
        $repovideos = $this->getDoctrine()->getRepository(Videos::class);
        $videos = $repovideos->findAll();

        $selVideos = [];
        foreach ($videos as $video) {
            // \dump($video);

            //    $videoTrick = $video->getTrick();

            $videorepoTrickId = $video->getTrickId();
            // \dump($videorepoTrickId);

            // \dump($videorepoTrickId);
            // dump($id);
            // die;
            \dump($videorepoTrickId);
            $id = (int) $id;

            // dump($id);
            // die;
            if ($id === $videorepoTrickId) {

                // $selVideos[] = $video;
                $selVideos[] = $video;
                \dump($selVideos);

                $videoname = $video->getUrl();

            }

        }

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
            'selVideos' => $selVideos,
        ]);

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
            \dump($user->getId());
            die;
            // $comment->setUserId();
            $trick->setComments($comment);
            \dump($comment);
            die;
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
