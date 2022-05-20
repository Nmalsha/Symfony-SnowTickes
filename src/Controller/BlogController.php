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

        $donees = $repo->findBy([], ['createdOn' => 'desc']);
        $repoImage = $this->getDoctrine()->getRepository(Images::class);
        $Images = $repoImage->findAll();
        $repoUser = $this->getDoctrine()->getRepository(User::class);
        $user = $repoUser->findAll();
        $tricks = $paginator->paginate(
            $donees,
            $request->query->getInt('page', 1),

        );

        return $this->render('blog/home.html.twig', [
            'controller_name' => "BlogController",
            'tricks' => $tricks,
            'images' => $Images,
            'user' => $user,
        ]);
    }

}
