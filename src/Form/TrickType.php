<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TrickName')
            ->add('description')
            ->add('categorie')

            ->add('images', FileType::class, [
                'multiple' => true,

                'label' => 'Main Image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('gallaryimages', FileType::class, [
                'multiple' => true,

                'label' => 'Gallary images',
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
