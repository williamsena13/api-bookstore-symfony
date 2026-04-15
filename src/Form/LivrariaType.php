<?php

namespace App\Form;

use App\Entity\Livraria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class LivrariaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Informações gerais
            ->add('nome', TextType::class, ['label' => 'Nome da Livraria', 'attr' => ['class' => 'form-control']])
            ->add('descricao', TextareaType::class, ['label' => 'Descrição', 'required' => false, 'attr' => ['class' => 'form-control', 'rows' => 3]])
            ->add('telefone', TextType::class, ['label' => 'Telefone', 'required' => false, 'attr' => ['class' => 'form-control', 'placeholder' => '(00) 00000-0000', 'data-mask' => 'phone']])
            ->add('email', EmailType::class, ['label' => 'E-mail', 'required' => false, 'attr' => ['class' => 'form-control']])

            // Endereço
            ->add('cep', TextType::class, ['label' => 'CEP', 'required' => false, 'attr' => ['class' => 'form-control', 'placeholder' => '00000-000', 'data-viacep' => 'cep', 'maxlength' => 9]])
            ->add('logradouro', TextType::class, ['label' => 'Logradouro', 'required' => false, 'attr' => ['class' => 'form-control', 'data-viacep' => 'logradouro']])
            ->add('numero', TextType::class, ['label' => 'Número', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('complemento', TextType::class, ['label' => 'Complemento', 'required' => false, 'attr' => ['class' => 'form-control', 'data-viacep' => 'complemento']])
            ->add('bairro', TextType::class, ['label' => 'Bairro', 'required' => false, 'attr' => ['class' => 'form-control', 'data-viacep' => 'bairro']])
            ->add('cidade', TextType::class, ['label' => 'Cidade', 'required' => false, 'attr' => ['class' => 'form-control', 'data-viacep' => 'localidade']])
            ->add('uf', TextType::class, ['label' => 'UF', 'required' => false, 'attr' => ['class' => 'form-control', 'maxlength' => 2, 'data-viacep' => 'uf']])
            ->add('latitude', HiddenType::class, ['required' => false])
            ->add('longitude', HiddenType::class, ['required' => false])

            // Identidade visual
            ->add('faviconFile', FileType::class, [
                'label' => 'Favicon',
                'mapped' => false,
                'required' => false,
                'constraints' => [new Image(['maxSize' => '1M', 'mimeTypesMessage' => 'Envie uma imagem válida.'])],
                'attr' => ['class' => 'form-control', 'accept' => 'image/*'],
            ])
            ->add('logoNavbarFile', FileType::class, [
                'label' => 'Logo do Navbar',
                'mapped' => false,
                'required' => false,
                'constraints' => [new Image(['maxSize' => '2M', 'mimeTypesMessage' => 'Envie uma imagem válida.'])],
                'attr' => ['class' => 'form-control', 'accept' => 'image/*'],
            ])

            // Tema
            ->add('corPrimaria', ColorType::class, ['label' => 'Cor Primária', 'required' => false, 'attr' => ['class' => 'form-control form-control-color']])
            ->add('corSecundaria', ColorType::class, ['label' => 'Cor Secundária', 'required' => false, 'attr' => ['class' => 'form-control form-control-color']])
            ->add('corSidebar', ColorType::class, ['label' => 'Cor do Sidebar', 'required' => false, 'attr' => ['class' => 'form-control form-control-color']])
            ->add('temaAdmin', ChoiceType::class, [
                'label' => 'Tema Admin',
                'required' => false,
                'choices' => ['Claro' => 'light', 'Escuro' => 'dark'],
                'attr' => ['class' => 'form-select', 'data-no-ts' => '1'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Livraria::class]);
    }
}
