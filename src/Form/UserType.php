<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $this->container->getParameter('security.role_hierarchy.roles'),
        $builder
            ->add('email')
            ->add('name')
            ->add('lastName', TextType::class, [
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_ALLOWED_TO_SWITCH' => 'ROLE_ALLOWED_TO_SWITCH',
                    'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                ],
                'multiple'=> true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Поля паролей должны совпадать.',
                'mapped' => false,
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'пожалуйста, введите пароль',
//                    ]),
//                    new Length([
//                        'min' => 6,
//                        'minMessage' => 'Ваш пароль должен быть не менее {{ limit }} символов',
//                        // max length allowed by Symfony for security reasons
//                        'max' => 4096,
//                    ]),
//                ],
            ])
            ->add('isVerified')
            ->add('userpic', FileType::class, [
                'mapped'   => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png',
                            'image/svg+xml',
                            'image/tiff',
                            'image/vnd.microsoft.icon',
                            'image/vnd.wap.wbmp',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Только изображения',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }
}
