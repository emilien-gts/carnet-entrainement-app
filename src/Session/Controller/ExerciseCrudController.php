<?php

namespace App\Session\Controller;

use App\Core\Controller\BaseCrudController;
use App\Session\Entity\Exercise;
use App\Session\Enum\MuscleEnum;
use App\Session\ExerciseFieldsConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

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
        /** @var Exercise $exercise */
        $exercise = $this->getContext()?->getEntity()?->getInstance();

        return ExerciseFieldsConfigurator::configureFields($exercise);
    }
}
