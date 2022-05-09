<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // ->add('firstName')
        // ->add('secondName')
        // ->add('email')
        ->add('profieImage', FileType::class, [
            'multiple' => true,

            'label' => 'profile_image',
            'mapped' => false,
            'required' => false,
        ])
        // ->add('password')
        // ->add('roles')
        // ->add('activation_token')
        // ->add('reset_token')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
