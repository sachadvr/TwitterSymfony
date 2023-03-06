<?php

namespace App\Twig\Components;

use App\Entity\Commentaires;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
#[AsTwigComponent('commentaires')]
final class CommentairesComponent
{
    public Commentaires $commentaire;
    private EntityManagerInterface $em;

    public function __construct(Commentaires $commentaire, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->commentaire = $commentaire;
    }
    public function replacedContent($contenu) {
        // Search for usernames in the contenu column
        $contenu = '@'. $this->commentaire->getLinkedPost()->getCreatedBy()->getUsername() . ' ' . $contenu;
        preg_match_all('/@(\w+)/', $contenu, $matches);
        $usernames = $matches[1];
    
        // Replace usernames with links to the user profile if the user exists in the database
        foreach ($usernames as $username) {
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
            if ($user) {
                $link = '<a href="__link__" class="text-blue-500">@' . $username . '</a>';
                $contenu = str_replace('@' . $username, $link, $contenu);
                $contenu = str_replace('__link__', '/user/' . $username, $contenu);
            }
        }
    
        return $contenu;
    }
}
