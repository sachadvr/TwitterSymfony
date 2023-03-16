<?php

namespace App\Controller;

use App\Custom\LastRoute;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class ProfileController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/user/{username}', name: 'app_profile')]
    public function index(Request $request, $username): Response
    {
        $tabs = $request->query->get('tabs');
        if ($tabs == null) {
            $tabs = 1;
        }
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user) return $this->redirectToRoute('app_post');
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'now' => new \DateTimeImmutable(),
            'tabs' => $tabs,
        ]);
    }
    // app_follow
    #[Route('/user/{username}/follow', name: 'app_follow')]
    public function follow(Request $request, $username, UserRepository $ur): Response
    {
        $follow = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        
        $follower = $ur->findByIdentifier($this->getUser()->getUserIdentifier());
        if (!$follow || !$follower) {
            return $this->redirectToRoute('app_post');
        }
        if ($follow != $follower) {
        
        if ($follow->getFollowers()->contains($follower)) {
            $follow->removeFollower($follower);
            $follower->removeFollowing($follow);
        } else {
            $follow->addFollower($follower);
            $follower->addFollowing($follow);
        }
        

        $this->em->persist($follow);

        $this->em->flush();

        }
        $route = new LastRoute();
        return $this->redirect($route->getLastRoute($request));
    }

}
