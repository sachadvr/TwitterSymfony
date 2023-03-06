<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NewTweetType;
use App\Entity\User;
use App\Form\NewCommentairesType;

class PostController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    
    #[Route('/', name: 'app_post', methods: ['GET','POST'])]
    public function index(Request $request)
    {
        $post = $this->em->getRepository(Post::class)->findBy([], ['lastModified' => 'DESC']);
        // i want to findby createdby Desc and if the post has been retweeted by following of the user make it appear first
        // $form from NewTweetType
        if ($this->getUser()) {

            $user = $this->getUser()->getUserEntity();
            $following = $user->getFollowing();
            
            $qb = $this->em->createQueryBuilder();
            if ($following->count() > 0) {
                // j'ai tenté de faire qq chose pour les followers mais je ne sais pas comment faire
                // donc j'ai fait en sorte que les following soient affichés en premier
                // ce qui est un peu logique car on suit les gens pour voir leurs tweets
                $qb->select('p')
                ->from(Post::class, 'p')
                ->where('p.createdBy IN (:following)')
                ->setParameter('following', $following)
                ->orderBy('p.lastModified', 'DESC');
                $merged = array_merge($qb->getQuery()->getResult(), $post);
                $post = array_unique($merged, SORT_REGULAR);
            }
        }
        $form = $this->createForm(NewTweetType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setLastModified(new \DateTimeImmutable());
            $post->setCreatedBy($this->getUser());
            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute('app_post');
        }
        return $this->render('Posts/post.html.twig', array(
            'posts' => $post,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, true),
        ));
    }

    #[Route('/post/{id}', name: 'app_post_show', methods: ['GET','POST'])]
    public function show($id, Request $request)
    {
        $post = $this->em->getRepository(Post::class)->find($id);

        $form = $this->createForm(NewCommentairesType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $form->getData();
            $commentaire->setCreatedBy($this->getUser());
            $commentaire->setLinkedPost($post);
            $commentaire->setCreatedAt(new \DateTimeImmutable());
            $this->em->persist($commentaire);
            $this->em->flush();
            return $this->redirectToRoute('app_post_show', ['id' => $id]);
        }

        return $this->render('Posts/show.html.twig', [
            'post' => $post,
            'commentaires' => $this->em->getRepository(Commentaires::class)->findBy(['LinkedPost' => $post], ['createdAt' => 'DESC']),
            'form' => $form->createView(),
            'now' => new \DateTimeImmutable(),
            'errors' => $form->getErrors(true, true),

        ]);
    }
    // retweet
    #[Route('/post/{id}/retweet', name: 'app_post_retweet', methods: ['GET'])]
    public function retweet($id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $isRetweeted = $post->getRetweet()->contains($this->getUser());
        if ($isRetweeted) {
            $post->removeRetweet($this->getUser());
        } else {
            $post->addRetweet($this->getUser());
            $post->setLastModified(new \DateTimeImmutable());
        }
        $this->em->flush();
        
        return $this->redirectToRoute('app_post');
    }
    // like
    #[Route('/post/{id}/like', name: 'app_post_like', methods: ['GET'])]
    public function like($id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $isLiked = $post->getLikes()->contains($this->getUser());
        if ($isLiked) {
            $post->removeLike($this->getUser());
        } else {
            $post->addLike($this->getUser());
            $post->setLastModified(new \DateTimeImmutable());
        }
        $this->em->flush();
        return $this->redirectToRoute('app_post');
    }
}
