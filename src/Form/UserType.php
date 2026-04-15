<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome', TextType::class, [
                'label' => 'Nome',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nome completo'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => ['class' => 'form-control', 'placeholder' => 'email@exemplo.com'],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => $options['is_new'],
                'first_options' => [
                    'label' => 'Senha',
                    'attr' => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirmar Senha',
                    'attr' => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'As senhas não conferem.',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Perfil',
                'choices' => ['Administrador' => 'ROLE_ADMIN', 'Usuário' => 'ROLE_USER'],
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class, 'is_new' => true]);
    }
}
