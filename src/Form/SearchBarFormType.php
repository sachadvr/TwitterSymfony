<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBarFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchbar', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'pl-8 w-full bg-[var(--bg-color)] rounded-md py-2',
                    'onkeypress' => 'if(!event.shiftKey && event.keyCode == 13) { document.querySelector(".searchbtn").click(); event.preventDefault(); }'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
