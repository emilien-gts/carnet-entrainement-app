<?php

namespace App\Session\Form;

use App\Session\Entity\Exercise;
use App\Session\Entity\SessionExercise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionExerciseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('exercise', EntityType::class, [
            'label' => 'label.exercise',
            'class' => Exercise::class,
        ]);

        $builder->add('nbSet', IntegerType::class, [
            'label' => 'label.nbSet',
        ]);

        $builder->add('nbReps', IntegerType::class, [
            'label' => 'label.nbReps',
        ]);

        $builder->add('tempo', IntegerType::class, [
            'label' => 'label.tempo',
        ]);

        $builder->add('number', IntegerType::class, [
            'label' => 'label.number',
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => 'label.description',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SessionExercise::class);
    }
}
