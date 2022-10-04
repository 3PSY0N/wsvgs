<?php

namespace App\Form;

use App\Entity\Icon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class IconType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => "can't be blank"])
                ]
            ])
            ->add('slug')
            ->add('svg', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => "can't be blank"])
                ]
            ])
            ->add('categories')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Icon::class,
        ]);
    }
}
