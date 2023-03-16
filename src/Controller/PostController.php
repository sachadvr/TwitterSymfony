<?php

namespace App\Controller;

use App\Custom\ImageOptimizer;
use App\Entity\Commentaires;
use App\Entity\Hashtag;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NewTweetType;
use App\Entity\User;
use App\Form\NewCommentairesType;
use App\Form\SearchBarFormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Uid\Uuid;

use App\Custom\LastRoute;

class PostController extends AbstractController
{
    private $em;
    private $postRepository;
    public function __construct(EntityManagerInterface $em, PostRepository $postRepository)
    {
        $this->em = $em;
        $this->postRepository = $postRepository;
    }
    
    
    #[Route('/', name: 'app_post', methods: ['GET','POST'])]
    public function index(Request $request)
    {
        $post = $this->em->getRepository(Post::class)->findBy([], ['lastModified' => 'DESC']);

        // test


        $username_list = null;
        $hashtag_list = null;
        if ($this->getUser()) {
            
            $username_list = $this->em->getRepository(User::class)->findAll();
            $username_list = array_map(function($user) {
                return $user->getUsername();
            }, $username_list);

            $hashtag_list = $this->em->getRepository(Hashtag::class)->findAll();
            $hashtag_list = array_map(function($hashtag) {
                return $hashtag->getName();
            }, $hashtag_list);
            $user = $this->getUser()->getUserEntity();
            $following = $user->getFollowing();
            
            $qb = $this->em->createQueryBuilder();
                $tabs = $request->query->get('tabs');
                if ($tabs == null) {
                    $qb->select('p')
                    ->from(Post::class, 'p')
                    ->where('p.createdBy IN (:following)')
                    ->setParameter('following', $following)
                    ->orderBy('p.lastModified', 'DESC');
                    $merged = array_merge($qb->getQuery()->getResult(), $post);
                    $post = array_unique($merged, SORT_REGULAR);
                }else if ($tabs == 1) {
                    $qb->select('p')
                    ->from(Post::class, 'p')
                    ->where('p.createdBy IN (:following)')
                    ->setParameter('following', $following)
                    ->orderBy('p.lastModified', 'DESC');
                    $post = $qb->getQuery()->getResult();
                    
                }
                // j'ai tenté de faire qq chose pour les followers mais je ne sais pas comment faire
                // donc j'ai fait en sorte que les following soient affichés en premier
                // ce qui est un peu logique car on suit les gens pour voir leurs tweets
                
            }
        $form = $this->createForm(NewTweetType::class);
        $form->handleRequest($request);

        $search = $this->createForm(SearchBarFormType::class);
        $search->handleRequest($request);
        if ($search->isSubmitted() && $search->isValid()) {
            $searchdata = $search->getData()['searchbar'];
            return $this->redirectToRoute('search_index', ['searchbar' => $searchdata]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setLastModified(new \DateTimeImmutable());
            $post->setCreatedBy($this->getUser());
            
            $image = $form->get('image')->getData();

            if ($image) {
                $image = $form->get('image')->getData();
                if ($image) {
                    $uuid = Uuid::v4();
                    $guessPath = $uuid->toRfc4122() . '.' . $image->guessExtension();
                    
    
                    try {
                        $image->move(
                            $this->getParameter('post_images_directory'),
                            $guessPath
                        );
                        new ImageOptimizer($this->getParameter('post_images_directory') . '/' . $guessPath);
                        $post->setImage($guessPath);
                    } catch (FileException $e) {
                        $form->addError(new FormError('Error uploading image'));
                    }
                }
            }
            // check in contenu hashtags
            $contenu = $post->getContenu();
            $hashtags = [];
            preg_match_all('/#([a-zA-Z0-9]+)/', $contenu, $matches);
            foreach ($matches[1] as $match) {
                $hashtags[] = $match;
            }
            $hashtagRepository = $this->em->getRepository(Hashtag::class);
            foreach ($hashtags as $hashtag) {
                $hashtagEntity = $hashtagRepository->findOneBy(['name' => $hashtag]);
                if (!$hashtagEntity) {
                    $hashtagEntity = new Hashtag();
                    $hashtagEntity->setName($hashtag);
                    $this->em->persist($hashtagEntity);
                }

                $post->addHashtag($hashtagEntity);
            }



            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute('app_post');
        }
        return $this->render('Posts/post.html.twig', array(
            'posts' => $post,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, true),
            'tabs' => ($request->query->get('tabs') == null) ? false : true,
            'username_list' => $username_list,
            'search' => $search->createView(),
            'searchdata' => $search->getData()['search'] ?? "test",
            'hashtag_list' => $hashtag_list,

        ));
    }

    #[Route('/post/{id}', name: 'app_post_show', methods: ['GET','POST'])]
    public function show($id, Request $request)
    {
    
        $post = $this->em->getRepository(Post::class)->find($id);
        if (!$post) {
            $route = new LastRoute();
            return $this->redirect($route->getLastRoute($request));

        }

        $username_list = null;
        $hashtag_list = null;
        if ($this->getUser()) {
            $username_list = $this->em->getRepository(User::class)->findAll();
            $username_list = array_map(function($user) {
                return $user->getUsername();
            }, $username_list);

            $hashtag_list = $this->em->getRepository(Hashtag::class)->findAll();
            $hashtag_list = array_map(function($hashtag) {
                return $hashtag->getName();
            }, $hashtag_list);
        }
        $form = $this->createForm(NewCommentairesType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $form->getData();
            $commentaire->setCreatedBy($this->getUser());
            $commentaire->setLinkedPost($post);
            $commentaire->setCreatedAt(new \DateTimeImmutable());

            $contenu = $commentaire->getContenu();
            $hashtags = [];
            preg_match_all('/#([a-zA-Z0-9]+)/', $contenu, $matches);
            foreach ($matches[1] as $match) {
                $hashtags[] = $match;
            }
            $hashtagRepository = $this->em->getRepository(Hashtag::class);
            foreach ($hashtags as $hashtag) {
                $hashtagEntity = $hashtagRepository->findOneBy(['name' => $hashtag]);
                if (!$hashtagEntity) {
                    $hashtagEntity = new Hashtag();
                    $hashtagEntity->setName($hashtag);
                    $this->em->persist($hashtagEntity);
                }

                $commentaire->addHashtag($hashtagEntity);
            }


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
            'username_list' => $username_list,
            'hashtag_list' => $hashtag_list,
        ]);
    }
    // retweet
    #[Route('/post/{id}/retweet', name: 'app_post_retweet', methods: ['POST'])]
    public function retweet(Request $request, $id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->redirect('app_post');

        }
        $isRetweeted = $post->getRetweet()->contains($this->getUser());
        if ($isRetweeted) {
            $post->removeRetweet($this->getUser());
        } else {
            $post->addRetweet($this->getUser());
            $post->setLastModified(new \DateTimeImmutable());
        }
        $this->em->flush();
        

        $route = new LastRoute();
        return $this->redirect($route->getLastRoute($request));
    }
    // like
    #[Route('/post/{id}/like', name: 'app_post_like', methods: ['POST'])]
    public function like(Request $request, $id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->redirect('app_post');

        }

        $isLiked = $post->getLikes()->contains($this->getUser());
        if ($isLiked) {
            $post->removeLike($this->getUser());
        } else {
            $post->addLike($this->getUser());
            $post->setLastModified(new \DateTimeImmutable());
        }
        $this->em->flush();
        

        $route = new LastRoute();
        return $this->redirect($route->getLastRoute($request));
    }

    // delete
    #[Route('/post/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, $id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        if (!$post) {
            return $this->redirectToRoute('app_post');
        }
        // but if the user is an admin, he can delete the post
        if (($post->getCreatedBy() != $this->getUser()) && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $this->postRepository->remove($post, true);

        $route = new LastRoute();
        return $this->redirect($route->getLastRoute($request));
    }
}
