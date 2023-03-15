<?php

namespace App\Controller;

use App\Custom\LastRoute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DarkModeController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/dark', name: 'app_dark', methods: ['POST'])]
    public function index(Request $request) : Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            $currentdark = $user->getUserEntity()->getDarkMode();
            if ($currentdark == 1) {
                $user->getUserEntity()->setDarkMode(0);
            } else {
                $user->getUserEntity()->setDarkMode(1);
            }
        }
        
        $this->em->persist($user);
        $this->em->flush();

        $route = new LastRoute();
        return $this->redirect($route->getLastRoute($request));

    }
}
