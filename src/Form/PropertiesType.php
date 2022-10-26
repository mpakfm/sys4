<?php

namespace App\Form;

use App\Entity\Properties;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site_email')
            ->add('meta_title')
            ->add('meta_keywords')
            ->add('meta_description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Properties::class,
            'csrf_protection' => false,
        ]);
    }
}
