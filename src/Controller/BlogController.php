<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Trick;
use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(Request $request, PaginatorInterface $paginator): Response
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);

        $tricks = $repo->findBy([], ['createdOn' => 'desc']);
        $repoImage = $this->getDoctrine()->getRepository(Images::class);
        $Images = $repoImage->findBy(array('isMainImage' => '1'));

        $repoUser = $this->getDoctrine()->getRepository(User::class);
        $user = $repoUser->findAll();

        foreach ($tricks as &$trick) {
            foreach ($Images as $image) {
                if ($image->getTrick()->getId() === $trick->getId()) {

                    $trick->mainImage = $image;
                }
            }
        }

        return $this->render('blog/home.html.twig', [
            'controller_name' => "BlogController",
            'tricks' => $tricks,
            'images' => $Images,
            'user' => $user,
        ]);
    }

}
