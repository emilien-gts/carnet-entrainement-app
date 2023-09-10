<?php

namespace App\Session\Controller;

use App\Core\Controller\BaseCrudController;
use App\Session\Entity\Exercise;
use App\Session\Enum\MuscleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class ExerciseCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Exercise::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->renderContentMaximized();
        $crud->setEntityLabelInSingular('Exercice');
        $crud->setEntityLabelInPlural('Exercices');

        $crud->setDefaultSort(['name' => 'ASC']);

        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa fa-plus')->setLabel(false);
        });
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->setIcon('fa fa-edit');
        });

        $actions->disable(Action::DELETE);

        return $actions;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add(ChoiceFilter::new('mainMuscle')
            ->setChoices(MuscleEnum::filterChoices())
            ->canSelectMultiple()
        );

        return $filters;
    }

    public function configureFields(string $pageName): iterable
    {
        /** @var Exercise|null $exercise */
        $exercise = $this->getContext()?->getEntity()?->getInstance();

        yield TextField::new('name', 'label.name');

        yield TextareaField::new('description', 'label.description')
            ->hideOnIndex();

        yield ChoiceField::new('mainMuscle', 'label.mainMuscle')
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

        yield ChoiceField::new('secondaryMuscles', 'label.secondaryMuscles')
            ->hideOnIndex()
            ->allowMultipleChoices()
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => MuscleEnum::class,
                'required' => false,
                'choice_label' => function (MuscleEnum $choice, $key, $value) {
                    return $choice->label();
                },
                'choice_attr' => function (MuscleEnum $choice, $key, $value) use ($exercise) {
                    $attr = [];
                    if ($exercise && $choice === $exercise->mainMuscle) {
                        $attr['disabled'] = true;
                    }

                    return $attr;
                },
                'choices' => MuscleEnum::cases(),
            ]);
    }
}
