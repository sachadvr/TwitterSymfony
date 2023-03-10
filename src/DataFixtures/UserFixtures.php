<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Commentaires;
use App\Entity\Post;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Command\UserPasswordHashCommand;

class UserFixtures extends Fixture
{
    const ADMIN_USER_REFERENCE = 'sachauser';
    const ADMIN_USER_REFERENCE_2 = 'theouser';
    public function load(ObjectManager $manager): void
    {
       
        $user = new User();
        $user->setEmail("sachadvr@icloud.com");
        $user->setImagePath("sachadvr.jpg");
        $user->setBio("Je suis un développeur web");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setUsername("sachadvr");
        $user->setPassword('$2y$13$vDModiB4IcEl.riJ1CIcv.lyRNgQ36I5kqrTMKwAbZFizwianGhZe');
        $this->setReference(self::ADMIN_USER_REFERENCE, $user);

        $user2 = new User();
        $user2->setEmail("theo.dadon@gmail.com");
        $user2->setBio("Je suis pas un développeur web");
        $user2->setImagePath("theodadon.jpg");
        $user2->setRoles(["ROLE_USER"]);
        $user2->setUsername("theodadon");
        $user2->setPassword('$2y$13$vDModiB4IcEl.riJ1CIcv.lyRNgQ36I5kqrTMKwAbZFizwianGhZe');
        $this->setReference(self::ADMIN_USER_REFERENCE_2, $user2);


        $post = new Post();
        $post->setContenu('lorem ipsum dolor sit amet, consectetur adipiscing elit sed diam non  et justo ut magna.');
        $post->setCreatedAt(new DateTimeImmutable()); 
        $post->setLastModified(new DateTimeImmutable());
        $user2->addPost($post);
        $post->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE_2));
        
        $commentaire1 = new Commentaires();
        $commentaire1->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $commentaire1->setContenu("Hello, c'est totalement faux!");
        $commentaire1->setLinkedPost($post);
        $commentaire1->setCreatedAt(new DateTimeImmutable());
        $post->addCommentaire($commentaire1);
        $user->addCommentaire($commentaire1);

        $commentaire2 = new Commentaires();
        $commentaire2->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $commentaire2->setContenu("Hello, c'est totalement vrai!");
        $commentaire2->setCreatedAt(new DateTimeImmutable());
        $commentaire2->setLinkedPost($post);
        $post->addCommentaire($commentaire2);
        $user->addCommentaire($commentaire2);

        $post1 = new Post();
        $post1->setContenu('Contenu1 : ceci est un test décriture');
        $post1->setCreatedAt(new DateTimeImmutable());
        $post1->setLastModified(new DateTimeImmutable());
        $post1->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $user->addPost($post1);


        $post2 = new Post();
        $post2->setContenu('lorem ipsum');
        $post2->setCreatedAt(new DateTimeImmutable());
        $post2->setLastModified(new DateTimeImmutable());
        $post2->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE_2));
        $user2->addPost($post2);

        $post3 = new Post();
        $post3->setContenu('Contenu3');
        $post3->setCreatedAt(new DateTimeImmutable());
        $post3->setLastModified(new DateTimeImmutable());
        $post3->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $user->addPost($post3);

        $post4 = new Post();
        $post4->setContenu('Contenu1');
        $post4->setCreatedAt(new DateTimeImmutable());
        $post4->setLastModified(new DateTimeImmutable());
        $post4->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $user->addPost($post4);
        

        $manager->persist($post);
        $manager->persist($post1);
        $manager->persist($post2);
        $manager->persist($post3);
        $manager->persist($post4);
        $manager->persist($user);
        $manager->persist($user2);
        $manager->persist($commentaire1);
        $manager->persist($commentaire2);
        
        $manager->flush();
        
        
    }

    
    

}
