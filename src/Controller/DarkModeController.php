<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DarkModeController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/dark', name: 'app_dark')]
    public function index() : Response
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
        return $this->redirectToRoute('app_post');


    }
}
