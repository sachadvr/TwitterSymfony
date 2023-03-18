<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewTweetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $limit = 255;
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Écrivez votre tweet ici...',
                    'class' => 'w-full p-3 outline-none resize-none text-[var(--text-color)] bg-[var(--bg-color)]
                    focus:outline-none
                    ',
                    'onkeypress' => 'if(!event.shiftKey && event.keyCode == 13) { document.querySelector(".btn").click(); event.preventDefault(); }'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez écrire un commentaire',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre tweet doit contenir au moins {{ limit }} caractères',
                        'max' => $limit,
                        'maxMessage' => 'Votre tweet ne doit pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('allowcommentaire', CheckboxType::class, [
                'label' => 'Autoriser les commentaires',
                'attr' => [
                    'class' => 'text-[var(--text-color)] hidden',
                    'checked' => true,
                ],
                'mapped' => false,
                'required' => false,

            ])
            ->add('image', FileType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'hidden',
                    'accept' => 'image/*',
                ],
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
