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
use Knp\Component\Pager\PaginatorInterface;
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
    public function readTrick(string $slug,
        PaginatorInterface $paginator,
        Trick $trick,
        VideosRepository $videosRepository,
        ImagesRepository $imagesRepository,
        TrickRepository $trickRepository,
        CommentsRepository $commentRepository,
        Request $request,
        EntityManagerInterface $manager): Response {

        //get trick
        $trick = $trickRepository->findOneBy(['slug' => $slug]);

        //get images

        $selImages = $trick->getImages();
        //get main images
        foreach ($selImages as $mainimg) {
            if ($mainimg->getIsMainImage() === true) {
                $trick->mainImage = $mainimg;
            }

        }

        //get videos

        $selVideos = $trick->getVideos();

        //get comments
        $comments = $commentRepository->find(['id' => $trick->getId()]);
        $comments = $commentRepository->findBy([], ['createdAt' => 'desc']);

        $selComments = [];

        foreach ($comments as $comment) {

            if ($trick->getId() === $comment->getTrick()->getId()) {
                $selComments[] = $comment;

            }
        }
        //adding pagenation to the comments list
        $selCommentspag = $paginator->paginate(
            $selComments,
            $request->query->getInt('page', 1),
            6
        );

        //add comment
        $comments = new Comments;
        $form = $this->createForm(CommentsType::class, $comments);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comments->setCreatedAt(new \DateTimeImmutable());

            // get user
            $user = $this->getUser();
            //set user to the comment
            $comments->setUser($user);
            //set trick ti the comment
            $comments->setTrick($trick);
            //save to the DB
            $manager->persist($comments);
            $manager->flush();

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
            'selCommentspag' => $selCommentspag,

            'form' => $form->createView(),

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
        $slugname = $form->get("TrickName")->getData();
        //  check if the Trickname already existe
        $trckexist = $this->getDoctrine()->getRepository(Trick::class)->findBy(array('trickName' => $slugname));
        if (!$trckexist) {
            if ($form->isSubmitted() && $form->isValid()) {

                $trick->setUser($this->getUser());

                //get Main image data

                $mainimages = $form->get('images')->getData();

                //get image name
                if ($mainimages) {
                    $imageDocument = $mainimages->getClientOriginalName();

                    //send image name to the images folder
                    $mainimages->move(
                        $this->getParameter('images_directory'),
                        $imageDocument
                    );

                    // save image name to the DB
                    $img = new Images();
                    $img->setName($imageDocument);
                    $img->setIsMainImage(1);
                    $img->setTrick($trick);
                    $trick->addImage($img);

                }

                $gallaryImages = $form->get('gallaryimages')->getData();
                if ($gallaryImages) {
                    //get gallery images data

                    //loop true the images

                    foreach ($gallaryImages as $image) {

                        $galleryimageDocument = $image->getClientOriginalName();

                        //Save image to the directory
                        $image->move(
                            $this->getParameter('images_directory'),
                            $galleryimageDocument
                        );

                        // save image name to the DB
                        $img = new Images();
                        $img->setName($galleryimageDocument);

                        $img->setIsMainImage(0);
                        $img->setTrick($trick);
                        $trick->addImage($img);

                    }
                }

                //set slug name

                $trick->setslug($slugname);
                //set created date
                $trick->setCreatedOn(new \DateTime());

                //if the form is valid
                $manager->persist($trick);
                $manager->flush();

                $this->addflash(
                    'success',
                    "Le trick a été crée avec succès !"
                );
                //Redirect to the added trick view
                return $this->redirectToRoute('home');

            }
        } else {
            $this->addflash(
                'error',
                "Le nom de trick deja existe !"
            );
            return $this->redirectToRoute('trick_create');

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

        $url = $form->get('url')->getData();

        if ($url) {
            //set url to the video
            $video->setUrl($url);
            //set trick to the video
            $video->setTrick($trick);
            //save to the DB
            $manager->persist($video);
            $manager->flush();
            $this->addflash(
                'success',
                "Le video a été enregistrer avec succès !"
            );
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

        $videos = $repovideos->findAll();
        $selVideos = [];
        foreach ($videos as $video) {

            if ($trick->getId() === $video->getTrick()->getId()) {
                $selVideos[] = $video;
            }
        }
        //Main Images handling

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUser($this->getUser());

            //get Main image data
            $mainimages = $form->get('images')->getData();
            if ($mainimages) {
                $imageDocument = $mainimages->getClientOriginalName();

                //send image name to the images folder
                $mainimages->move(
                    $this->getParameter('images_directory'),
                    $imageDocument
                );

                // save image name to the DB
                $imgmain = new Images();
                $imgmain->setName($imageDocument);
                $imgmain->setIsMainImage(1);
                $imgmain->setTrick($trick);
                $trick->addImage($imgmain);
            }

            //Gallery Images handling
            $gallaryImages = $form->get('gallaryimages')->getData();

            //loop true the images
            if ($gallaryImages) {
                foreach ($gallaryImages as $image) {

                    $imageDocument = $image->getClientOriginalName();
                    $image->move(
                        $this->getParameter('images_directory'),
                        $imageDocument
                    );
                    // save image name to the DB

                    $img = new Images();
                    $img->setName($imageDocument);
                    $img->setIsMainImage(0);
                    $img->setTrick($trick);
                    $trick->addImage($img);

                }

            }
            //if the form is valid
            $manager->persist($trick);

            $manager->flush();

            $this->addflash(
                'success',
                "Le Trick a été modifier avec succès !"
            );
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

            }
        }

        //delete videos
        $videos = $repoVideo->findAll();

        foreach ($videos as $video) {

            if ($trick->getId() === $video->getTrick()->getId()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($video);

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
     * @Route("/tricks/supprime/mainimage/{id}", name="delete_main_image" , methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $request)
    {
        $reqData = $request->getContent();

        $data = json_decode($reqData, true);
//$imageId =
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

            $this->addflash(
                'success',
                "Le element a été supprimer avec succès !"
            );
        } else {

            $this->addflash(
                'error',
                "Le element n'a pas été supprimer !"
            );
        }
    }

    /**
     * @Route("/tricks/supprime/video/{id}", name="delete_video" , methods={"DELETE"})
     */
    public function deleteVideo(Videos $Videos, Request $request)
    {
        $reqData = $request->getContent();

        $data = json_decode($reqData, true);

        //check if the token valid
        if ($this->isCsrfTokenValid('delete' . $Videos->getId(), $data['_token'])) {

            //getting image name from the DB
            $name = $Videos->getUrl();
            //Deleting the image from the directory

            //deleting the image from the DB
            $em = $this->getDoctrine()->getManager();
            $em->remove($Videos);
            $em->flush();

            $this->addflash(
                'success',
                "Le element a été supprimer avec succès !"
            );
        } else {

            $this->addflash(
                'error',
                "Le element n'a pas été supprimer !"
            );
        }

    }

}
