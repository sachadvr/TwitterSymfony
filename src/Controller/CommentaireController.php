<?php

namespace App\Controller;

use App\Entity\Commentaires;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    #[Route('/comment/{id}/retweet', name: 'app_post_retweet', methods: ['GET'])]
    public function retweet($id)
    {
        $comment = $this->em->getRepository(Commentaires::class)->find($id);
        $isRetweeted = $comment->getRetweet()->contains($this->getUser());
        if ($isRetweeted) {
            $comment->removeRetweet($this->getUser());
        } else {
            $comment->addRetweet($this->getUser());
        }
        $this->em->flush();
        
        return $this->redirectToRoute('app_post');
    }
    // like
    #[Route('/commentaire/{id}/like', name: 'app_post_like', methods: ['GET'])]
    public function like($id)
    {
        $comment = $this->em->getRepository(Commentaires::class)->find($id);
        $isLiked = $comment->getLikes()->contains($this->getUser());
        if ($isLiked) {
            $comment->removeLike($this->getUser());
        } else {
            $comment->addLike($this->getUser());
        }
        $this->em->flush();
        return $this->redirectToRoute('app_post');
    }

    // delete
    #[Route('/commentaire/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete($id)
    {
        $comment = $this->em->getRepository(Commentaires::class)->find($id);
        if (($comment->getCreatedBy() != $this->getUser()) && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $this->comRepository->remove($comment, true);
        return $this->redirectToRoute('app_post');
    }
}
