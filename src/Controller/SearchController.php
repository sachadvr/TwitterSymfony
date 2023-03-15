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

    #[Route('/search/{searchbar}', name: 'search_index')]
    public function index($searchbar): Response
    {

        // $users = $this->em->getRepository(User::class)->findBy(['username' => $searchbar]);
        // $posts = $this->em->getRepository(Post::class)->findBy(['contenu' => $searchbar]);
        // $comments = $this->em->getRepository(Commentaires::class)->findBy(['content' => $searchbar]);

        //now that that starts with searchbar or contains searchbar
        $users = $this->em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.username LIKE :searchbar')
            ->setParameter('searchbar', '%'. $searchbar . '%')
            ->getQuery()
            ->getResult();
        $posts = $this->em->getRepository(Post::class)->createQueryBuilder('p')
            ->where('p.contenu LIKE :searchbar')
            ->setParameter('searchbar', '%'. $searchbar . '%')
            ->getQuery()
            ->getResult();
        $comments = $this->em->getRepository(Commentaires::class)->createQueryBuilder('c')
            ->where('c.contenu LIKE :searchbar')
            ->setParameter('searchbar', '%'. $searchbar . '%')
            ->getQuery()
            ->getResult();

        return $this->render('search/index.html.twig', [
            'users' => $users,
            'posts' => $posts,
            'comments' => $comments,
        ]);
    }

}
