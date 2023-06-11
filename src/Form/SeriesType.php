<?php

namespace App\Form;

use App\DTO\SeriesInputDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => ' ',
                'attr' => ['placeholder' => 'Nome da série '],
                'trim' => true
            ])
            ->add('seasonsQuantity', NumberType::class, [
                'label' => ' ',
                'attr' => ['placeholder' => 'Quantidade de temporadas '],
                'trim' => true
            ])
            ->add('episodesQuantity', NumberType::class, [
                'label' => ' ',
                'attr' => ['placeholder' => 'Episódios por temporada '],
                'trim' => true
            ])
            ->add('coverImage', FileType::class, [
                'label' => 'Imagem de Capa',
                'constraints' => [
                    new File(
                        maxSize: '2048k',
                        mimeTypes: 'image/*',
                        mimeTypesMessage: 'Somente arquivos de imagens são válidos'
                    )
                ],
                'required' =>  false
            ])
            ->add('save', SubmitType::class, ['label' => $options['is_edit'] ? 'Editar' : 'Adicionar'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeriesInputDto ::class,
            'is_edit' => false
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
