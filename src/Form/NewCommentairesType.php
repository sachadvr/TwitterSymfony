<?php

namespace App\Form;

use App\Entity\Commentaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
class NewCommentairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $limit = 255;
        $builder
        ->add('contenu', TextareaType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Répondre au tweet de @',
                'class' => 'focus:outline-none commentaire_area p-3 w-full outline-none resize-none text-[var(--text-color)] bg-[var(--bg-color)]',
                'onkeypress' => 'if(!event.shiftKey && event.keyCode == 13) { document.querySelector(".btn").click(); event.preventDefault(); }'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez écrire un commentaire',
                ]),
                new Length([
                    'max' => $limit,
                    'maxMessage' => 'Votre tweet ne doit pas dépasser {{ limit }} caractères',
                ]),
            ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }
}
