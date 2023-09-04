<?php

namespace App\Session;

use App\Core\Contracts\FieldsConfigurator;
use App\Session\Entity\Exercise;
use App\Session\Enum\MuscleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class ExerciseFieldsConfigurator implements FieldsConfigurator
{
    /**
     * @param Exercise $entity
     */
    public static function configureFields($entity, ?string $pageName = null): iterable
    {
        yield TextField::new('name', 'Nom');

        yield TextareaField::new('description', 'Description')
            ->hideOnIndex();

        yield ChoiceField::new('mainMuscle', 'Muscle principal travaillé')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => MuscleEnum::class,
                'choice_label' => function (MuscleEnum $choice, $key, $value) {
                    return $choice->label();
                },
                'choices' => MuscleEnum::cases(),
            ])
            ->formatValue(function ($value, Exercise $entity) {
                if (null === $entity->mainMuscle) {
                    return '';
                }

                return \sprintf(
                    '<span class="badge badge-primary">%s</span>',
                    $entity->mainMuscle->label(),
                );
            });

        yield ChoiceField::new('secondaryMuscles', 'Muscles secondaires travaillés')
            ->hideOnIndex()
            ->allowMultipleChoices()
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => MuscleEnum::class,
                'choice_label' => function (MuscleEnum $choice, $key, $value) {
                    return $choice->label();
                },
                'choice_attr' => function (MuscleEnum $choice, $key, $value) use ($entity) {
                    $attr = [];
                    if ($choice === $entity->mainMuscle) {
                        $attr['disabled'] = true;
                    }

                    return $attr;
                },
                'choices' => MuscleEnum::cases(),
            ]);
    }
}
