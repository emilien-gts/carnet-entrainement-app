<?php

namespace App\Training\Form;

use App\Training\Entity\TrainingExerciseSet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainingSetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('setNumber', IntegerType::class, [
            'label' => 'label.setNumber',
        ]);

        $builder->add('nbReps', IntegerType::class, [
            'label' => 'label.nbReps',
        ]);

        $builder->add('weight', IntegerType::class, [
            'label' => 'label.weight',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', TrainingExerciseSet::class);
    }
}
