<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Username',
                'class' => 'border',
                'value' => $options['data']->getUsername()
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un nom d\'utilisateur',
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'Votre nom d\'utilisateur doit faire au moins {{ limit }} caractères',
                    'max' => 255,
                    'maxMessage' => 'Votre nom d\'utilisateur ne peut pas faire plus de {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Email',
                'class' => 'border',
                'value' => $options['data']->getEmail()
            ],
            'constraints' => [
                new Length([
                    'min' => 3,
                    'minMessage' => 'Votre email doit faire au moins {{ limit }} caractères',
                    'max' => 255,
                    'maxMessage' => 'Votre email ne peut pas faire plus de {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('plainPassword', PasswordType::class, [
            'label' => false,
            'mapped' => false,
            'required' => false,
            'attr' => ['class' => 'border',
            'placeholder' => 'Mot de passe'],
            
            'constraints' => [
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                    'max' => 4096,
                    'maxMessage' => 'Votre mot de passe ne peut pas faire plus de {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('bio', TextareaType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Description',
                'class' => 'border'
            ],
            'constraints' => [
                new Length([
                    'max' => 255,
                    'maxMessage' => 'Votre description ne peut pas faire plus de {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('image', FileType::class, [
            'label' => false,
            'mapped' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Image',
                'accept' => 'image/*',
                'class' => 'hidden',
            
            ],
            
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
