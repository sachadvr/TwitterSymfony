<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Uid\Uuid;
use App\Custom\ImageOptimizer;
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            try{
                return $this->redirectToRoute('target_path');
            }catch(\Exception $e){
                return $this->redirectToRoute('app_post');
            }
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            // $email = $form->get('email')->getData();
            // get errors
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $image = $form->get('image')->getData();
            if ($image) {
                $uuid = Uuid::v4();
                $extension = $image->guessExtension();
                if ($extension == null) {
                    $extension = 'jpg';
                }
                $guessPath = $uuid->toRfc4122() . '.' . $extension;
                

                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $guessPath
                    );
                    new ImageOptimizer($this->getParameter('images_directory') . '/' . $guessPath);
                    $user->setImagePath($guessPath);
                } catch (FileException $e) {
                    throw new \Exception('Erreur lors de l\'upload de l\'image');
                }
            }


            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'errors' => $form->getErrors(true, true),
        ]);
    }
}
