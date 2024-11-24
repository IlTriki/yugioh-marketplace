<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => [
                    'placeholder' => 'email@example.com',
                    'autocomplete' => 'email'
                ]
            ])
            ->add('firstName', null, [
                'attr' => [
                    'placeholder' => 'First Name',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'First Name is required']),
                ],
            ])
            ->add('lastName', null, [
                'attr' => [
                    'placeholder' => 'Last Name',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Last Name is required']),
                ],
            ])
            ->add('username', null, [
                'attr' => [
                    'placeholder' => 'Username',
                    'autocomplete' => 'username'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => '••••••••'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
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
