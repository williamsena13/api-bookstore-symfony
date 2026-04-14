<?php

namespace App\Form;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Editora;
use App\Entity\Livro;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Digite o título do livro'],
            ])
            ->add('isbn', TextType::class, [
                'label' => 'ISBN',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 978-3-16-148410-0'],
            ])
            ->add('anoPublicacao', IntegerType::class, [
                'label' => 'Ano de Publicação',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 2024'],
            ])
            ->add('preco', MoneyType::class, [
                'label' => 'Preço',
                'currency' => 'BRL',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('editora', EntityType::class, [
                'class' => Editora::class,
                'choice_label' => 'nome',
                'label' => 'Editora',
                'required' => false,
                'placeholder' => 'Selecione uma editora',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('autores', EntityType::class, [
                'class' => Autor::class,
                'choice_label' => 'nome',
                'label' => 'Autores',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-select', 'size' => 5],
            ])
            ->add('assuntos', EntityType::class, [
                'class' => Assunto::class,
                'choice_label' => 'descricao',
                'label' => 'Assuntos',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-select', 'size' => 5],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Livro::class]);
    }
}
