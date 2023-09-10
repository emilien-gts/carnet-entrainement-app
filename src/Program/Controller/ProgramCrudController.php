<?php

namespace App\Program\Controller;

use App\Core\Controller\BaseViewCrudController;
use App\Core\Trait\Controller\ArchiveBatchActionTrait;
use App\Core\Trait\Controller\FavoriteBatchActionTrait;
use App\Core\Utils;
use App\Program\Entity\Program;
use App\Program\Service\ProgramManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class ProgramCrudController extends BaseViewCrudController
{
    use FavoriteBatchActionTrait;
    use ArchiveBatchActionTrait;

    public function __construct(RequestStack $requestStack, private readonly ProgramManager $manager)
    {
        parent::__construct($requestStack);
    }

    public static function getEntityFqcn(): string
    {
        return Program::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->renderContentMaximized();
        $crud->setEntityLabelInSingular('label.program');
        $crud->setEntityLabelInPlural('label.programs');
        $crud->setSearchFields(['id', 'name', 'description']);

        $crud->overrideTemplate('crud/index', 'program/index.html.twig');

        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        // Favorite batch actions
        if ($this->isView(self::VIEW_NOT_ARCHIVED)) {
            $actions->addBatchAction(Action::new('removeFavoriteBatch', 'action.remove_favorite', 'fa fa-star-o')
                ->linkToCrudAction('removeFavoriteBatch'));
            $actions->addBatchAction(Action::new('addFavoriteBach', 'action.add_favorite', 'fa fa-star')
                ->linkToCrudAction('addFavoriteBach'));
        }

        // Duplicate action
        $actions->add(Crud::PAGE_INDEX, Action::new('duplicate', 'action.duplicate', 'fa fa-copy')
            ->linkToCrudAction('duplicate'));

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'label.name')
            ->setTemplatePath('field/program/name.html.twig')
            ->hideOnForm();

        yield TextField::new('name', 'label.name')
            ->hideOnIndex()
            ->setFormTypeOption('required', false);

        yield TextareaField::new('description', 'label.description')
            ->hideOnIndex();

        foreach (Utils::day_of_weeks() as $dayOfWeek) {
            yield AssociationField::new($dayOfWeek, 'label.'.$dayOfWeek)
                ->setFormTypeOption('required', false);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function duplicate(): Response
    {
        /** @var Program $program */
        $program = $this->getEntityInstance();
        $this->throwIfNull($program);

        $p = $this->manager->duplicate($program);
        $this->persistAndFlush($p);

        $targetUrl = $this->generateEaUrl(self::class, Crud::PAGE_EDIT, ['entityId' => $p->id]);

        return $this->redirect($targetUrl);
    }
}
