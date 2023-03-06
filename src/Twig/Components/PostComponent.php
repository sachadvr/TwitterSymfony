<?php

namespace App\Twig\Components;

use App\Entity\Post;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Entity\User;

#[AsTwigComponent('post', template: 'components/post.html.twig')]
final class PostComponent
{
    public Post $post;
    public DateTimeInterface $now;
    private EntityManagerInterface $em;
    public function __construct(Post $post, EntityManagerInterface $em)
    {
        $this->post = $post;
        $this->em = $em;
        $this->now = new \DateTimeImmutable();
    }
    public function replacedContent($contenu) {
        // Search for usernames in the contenu column
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
