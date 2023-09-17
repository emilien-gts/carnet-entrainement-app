<?php

namespace App\Training\Controller;

use App\Core\Controller\BaseCrudController;
use App\Training\Entity\Training;
use App\Training\Form\TrainingExerciseType;
use App\Training\Service\TrainingManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class TrainingCrudController extends BaseCrudController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TrainingManager $manager
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Training::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->renderContentMaximized();
        $crud->setEntityLabelInSingular('label.training');
        $crud->setEntityLabelInPlural('label.trainings');

        $crud->setDefaultSort(['date' => 'ASC']);

        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $actions->remove(Crud::PAGE_INDEX, Action::NEW);

        $actions->add(Crud::PAGE_INDEX, Action::new('action.add_session', 'action.add_session')
            ->setIcon('fa fa-plus')
            ->linkToCrudAction('new')
            ->setTemplatePath('training/actions/add_session.html.twig')
            ->createAsGlobalAction()
        );
        $actions->add(Crud::PAGE_INDEX, Action::new('action.add_program', 'action.add_program')
            ->setIcon('fa fa-plus')
            ->linkToCrudAction('new')
            ->setTemplatePath('training/actions/add_program.html.twig')
            ->createAsGlobalAction()
        );

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', 'label.date')
            ->hideWhenUpdating();

        yield AssociationField::new('session', 'label.session')
            ->hideWhenUpdating();

        yield AssociationField::new('program', 'label.program')
            ->hideWhenUpdating()
            ->hideOnIndex();

        yield CollectionField::new('exercises', 'label.exercises')
            ->hideOnIndex()
            ->hideWhenCreating()
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(TrainingExerciseType::class);
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        if (null === $entityDto->getFields()) {
            return parent::createNewFormBuilder($entityDto, $formOptions, $context);
        }

        $from = $this->requestStack->getMainRequest()?->query->get('from');
        /** @var FieldDto $field */
        foreach ($entityDto->getFields() as $field) {
            if (AssociationField::class === $field->getFieldFqcn() && $from !== $field->getProperty()) {
                $entityDto->getFields()->unset($field);
            }
        }

        return parent::createNewFormBuilder($entityDto, $formOptions, $context);
    }

    /**
     * @param Training $entityInstance
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->program) {
            $trainings = $this->manager->createFromProgram($entityInstance->program, $entityInstance->date); /* @phpstan-ignore-line */
            foreach ($trainings as $training) {
                $this->persistAndFlush($training);
            }

            return;
        }

        if ($entityInstance->session) {
            $training = $this->manager->createFromSession($entityInstance->session, $entityInstance->date); /* @phpstan-ignore-line */
            $this->persistAndFlush($training);
        }
    }
}
