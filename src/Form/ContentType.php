<?php

namespace App\Form;

use App\Entity\ContentBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('code')
            ->add('dt_create')
            ->add('dt_update')
            ->add('user_create')
            ->add('user_update')
            ->add('active')
            ->add('sort')
            ->add('meta_title_section')
            ->add('meta_title_element')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => ContentBlock::class,
            'csrf_protection' => false,
        ]);
    }
}
