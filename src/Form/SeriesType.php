<?php

namespace App\Form;

use App\Entity\Series;
use App\SeriesInputDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => ' ',
                'attr' => ['placeholder' => 'Series Name '],
                'trim' => true
            ])
            ->add('seasonsQuantity', NumberType::class, [
                'label' => ' ',
                'attr' => ['placeholder' => 'Seasons Quantity '],
                'trim' => true
            ])
            ->add('episodesQuantity', NumberType::class, [
                'label' => ' ',
                'attr' => ['placeholder' => 'Episodes per Season '],
                'trim' => true
            ])
            ->add('save', SubmitType::class, ['label' => $options['is_edit'] ? 'Edit' : 'Add'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeriesInputDto ::class,
            'is_edit' => false
        ]);
    }
}
