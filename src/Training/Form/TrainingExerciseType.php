<?php

namespace App\Training\Form;

use App\Session\Entity\Exercise;
use App\Training\Entity\TrainingExercise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainingExerciseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('exercise', EntityType::class, [
            'label' => 'label.exercise',
            'class' => Exercise::class,
        ]);

        $builder->add('tempo', IntegerType::class, [
            'label' => 'label.tempo',
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => 'label.description',
        ]);

        $builder->add('sets', CollectionType::class, [
            'entry_type' => TrainingSetType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', TrainingExercise::class);
    }
}
