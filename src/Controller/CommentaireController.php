<?php

namespace App\Controller;

use App\Custom\LastRoute;
use App\Entity\Commentaires;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentairesRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class CommentaireController extends AbstractController
{
    private $em;
    private $comRepository;
    public function __construct(EntityManagerInterface $em, CommentairesRepository $comRepository)
    {
        $this->em = $em;
        $this->comRepository = $comRepository;
    }
    
    

    // retweet
    #[Route('/comment/{id}/retweet', name: 'app_comment_retweet', methods: ['GET'])]
    public function retweet(Request $request, $id)
    {
        $comment = $this->em->getRepository(Commentaires::class)->find($id);
        if (!$comment) return $this->redirectToRoute('app_post');
        $postLinked = $comment->getLinkedPost();
        $isRetweeted = $comment->getRetweet()->contains($this->getUser());
        if ($isRetweeted) {
            $comment->removeRetweet($this->getUser());
        } else {
            $comment->addRetweet($this->getUser());
        }
        $this->em->flush();
        
        $route = new LastRoute();
        return $this->redirect($route->getLastRoute($request));
    }
    // like
    #[Route('/comment/{id}/like', name: 'app_comment_like', methods: ['GET'])]
    public function like(Request $request, $id)
    {
        $comment = $this->em->getRepository(Commentaires::class)->find($id);
        if (!$comment) return $this->redirectToRoute('app_post');
        $postLinked = $comment->getLinkedPost();
        $isLiked = $comment->getLikes()->contains($this->getUser());
        if ($isLiked) {
            $comment->removeLike($this->getUser());
        } else {
            $comment->addLike($this->getUser());
        }
        $this->em->flush();
        
        $route = new LastRoute();
        return $this->redirect($route->getLastRoute($request));
    }

    // delete
    #[Route('/comment/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, $id)
    {
        $comment = $this->em->getRepository(Commentaires::class)->find($id);
        if (!$comment) return $this->redirectToRoute('app_post');
        $postLinked = $comment->getLinkedPost();
        if (($comment->getCreatedBy() != $this->getUser()) && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $this->comRepository->remove($comment, true);
       
        $route = new LastRoute();
        return $this->redirect($route->getLastRoute($request));

    }
}
