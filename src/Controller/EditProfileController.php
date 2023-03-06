<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/edit')]
class EditProfileController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_edit_profile_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('edit_profile/index.html.twig', [
            'user' => $userRepository->findOneBy(['username' => $this->getUser()->getUsername()]),
        ]);
    }

    

   

    #[Route('/edition', name: 'app_edit_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,UserPasswordHasherInterface $userPasswordHasher ): Response
    {
        $user = $this->getUser()->getUserEntity();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //check if the email is already used or username
            
            if ($form->get('plainPassword')->getData() != null || $form->get('plainPassword')->getData() != '') {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                    )
                );
            }
            $this->em->persist($user);
            $this->em->flush();

            
            
            return $this->redirectToRoute('app_edit_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('edit_profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'errors' => $form->getErrors(true, true),
        ]);
    }

    #[Route('/delete', name: 'app_edit_profile_delete', methods: ['POST'])]
    public function delete(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUsername()]);
        
        $user = $this->em->getRepository(User::class)->find($user->getId());

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $request->getSession()->invalidate();
            $userRepository->removeUser($user);
            $this->em->flush();

        }
        

        return $this->redirectToRoute('app_post', [], Response::HTTP_SEE_OTHER);
    }
}
