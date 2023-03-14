<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Entity\Followers;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class SearchController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/search/{str}', name: 'search')]
    public function index(Request $request, $str): Response
    {
        // $users = $this->em->getRepository(User::class)->findBy(['username' => $str]);
        // $posts = $this->em->getRepository(Post::class)->findBy(['contenu' => $str]);
        // $comments = $this->em->getRepository(Commentaires::class)->findBy(['content' => $str]);

        //now that that starts with str or contains str
        $users = $this->em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.username LIKE :str')
            ->setParameter('str', '%'. $str . '%')
            ->getQuery()
            ->getResult();
        $posts = $this->em->getRepository(Post::class)->createQueryBuilder('p')
            ->where('p.contenu LIKE :str')
            ->setParameter('str', '%'. $str . '%')
            ->getQuery()
            ->getResult();
        $comments = $this->em->getRepository(Commentaires::class)->createQueryBuilder('c')
            ->where('c.contenu LIKE :str')
            ->setParameter('str', '%'. $str . '%')
            ->getQuery()
            ->getResult();

        dd($users, $posts, $comments);
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

}
