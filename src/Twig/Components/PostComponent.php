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
    public int $showcomments;
    public int $redirect;
    
    public function __construct(Post $post, EntityManagerInterface $em)
    {
        $this->post = $post;
        $this->em = $em;
        $this->now = new \DateTimeImmutable();
    }

    
    // =========================================================== //
    // Functions to get the number of likes, comments and retweets //
    // =========================================================== //

    public function getFollowingLikes(User $user) {
        $likes = $this->post->getLikes();
        $following = $user->getFollowing();
        $likedBy = [];
        foreach ($following as $follow) {
            if ($likes->contains($follow)) {
                if ($this->post->getCreatedBy() != $follow) {
                    $likedBy[] = $follow->getUsername();
                }
            }
        }
        $likedBy = array_unique($likedBy);
        return $likedBy;
    }

    
    public function getFollowingComments(User $user) {
        $comments = $this->post->getCommentaires();
        $following = $user->getFollowing();
        $commentedBy = [];
        foreach ($following as $follow) {
            foreach ($comments as $comment) {
                if ($comment->getCreatedBy() == $follow) {
                    $commentedBy[] = $follow->getUsername();
                }
            }
        }
        $commentedBy = array_unique($commentedBy);
        return $commentedBy;
    }
    
    public function getFollowingRetweets(User $user) {
        $retweets = $this->post->getRetweet();
        $following = $user->getFollowing();
        $retweetedBy = [];
        foreach ($following as $follow) {
            if ($retweets->contains($follow)) {
                $retweetedBy[] = $follow->getUsername();
            }
        }
        $retweetedBy = array_unique($retweetedBy);
        return $retweetedBy;
    }
    
    //  function this.getFollowingComments(app.user).contains(comment.createdBy))
    public function containsComment(User $user, User $otherUser) {
        $comments = $this->getFollowingComments($user);
        if (in_array($otherUser->getUsername(), $comments)) {
            return true;
        }else {
            return false;
        }
    }
    // ====================================================== //
    //     See who liked, commented or retweeted the post     //
    // ====================================================== //

    public function seeWhoLiked(User $user) {
        $likedBy = $this->getFollowingLikes($user);
        
        if (count($likedBy) == 0)
        {
            return null;
        }elseif (count($likedBy) == 1) {
            $str= implode(', ', $likedBy) . " a aimé ce tweet";
            return $str;
        }else {
            $str= implode(', ', $likedBy) . " ont aimé ce tweet";
            return $str;
        }
        


    }
    public function seeWhoRetweeted(User $user) {
        $retweetedBy = $this->getFollowingRetweets($user);
        
        if (count($retweetedBy) == 0)
        {
            return null;
        }elseif (count($retweetedBy) == 1) {
            $str= implode(', ', $retweetedBy) . " a retweeté ce tweet";
            return $str;
        }else {
            $str= implode(', ', $retweetedBy) . " ont retweeté ce tweet";
            return $str;
        }
    }

    public function seeWhoCommented(User $user) {
        $commentedBy = $this->getFollowingComments($user);
        
        if (count($commentedBy) == 0)
        {
            return null;
        }elseif (count($commentedBy) == 1) {
            $str= implode(', ', $commentedBy) . " a commenté ce tweet";
            return $str;
        }else {
            $str= implode(', ', $commentedBy) . " ont commenté ce tweet";
            return $str;
        }
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

        // same for hashtags
        preg_match_all('/#(\w+)/', $contenu, $matches);
        $hashtags = $matches[1];
        foreach ($hashtags as $hashtag) {
            $link = '<a href="__link__" class="text-blue-500">#' . $hashtag . '</a>';
            $contenu = str_replace('#' . $hashtag, $link, $contenu);
            $contenu = str_replace('__link__', '/tag/' . $hashtag, $contenu);
        }
    
        return $contenu;
    }
}
