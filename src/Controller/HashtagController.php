<?php

namespace App\Controller;

use App\Custom\LastRoute;
use App\Repository\HashtagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HashtagController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/tag/{hashtag}', name: 'app_hashtag')]
    public function index(Request $request, HashtagRepository $hashRepository, $hashtag): Response
    {
        $hash = $hashRepository->findOneBy(['name' => $hashtag]);
        if (!$hash) {
            $route = new LastRoute();
            return $this->redirect($route->getLastRoute($request));
        }
        

        return $this->render('hashtag/index.html.twig', [
            'hash' => $hash,
        ]);
    }
}
