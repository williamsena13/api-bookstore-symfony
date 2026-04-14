<?php

namespace App\Form;

use App\Entity\Livraria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivrariaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome', TextType::class, [
                'label' => 'Nome da Livraria',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('descricao', TextareaType::class, [
                'label' => 'Descrição',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('telefone', TextType::class, [
                'label' => 'Telefone',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => '(00) 00000-0000', 'data-mask' => 'phone'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('cep', TextType::class, [
                'label' => 'CEP',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => '00000-000', 'data-viacep' => 'cep', 'maxlength' => 9],
            ])
            ->add('logradouro', TextType::class, [
                'label' => 'Logradouro',
                'required' => false,
                'attr' => ['class' => 'form-control', 'data-viacep' => 'logradouro'],
            ])
            ->add('numero', TextType::class, [
                'label' => 'Número',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('complemento', TextType::class, [
                'label' => 'Complemento',
                'required' => false,
                'attr' => ['class' => 'form-control', 'data-viacep' => 'complemento'],
            ])
            ->add('bairro', TextType::class, [
                'label' => 'Bairro',
                'required' => false,
                'attr' => ['class' => 'form-control', 'data-viacep' => 'bairro'],
            ])
            ->add('cidade', TextType::class, [
                'label' => 'Cidade',
                'required' => false,
                'attr' => ['class' => 'form-control', 'data-viacep' => 'localidade'],
            ])
            ->add('uf', TextType::class, [
                'label' => 'UF',
                'required' => false,
                'attr' => ['class' => 'form-control', 'maxlength' => 2, 'data-viacep' => 'uf'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Livraria::class]);
    }
}
