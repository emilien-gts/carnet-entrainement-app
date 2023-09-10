<?php

namespace App\Session\Controller;

use App\Core\Controller\BaseCrudController;
use App\Session\Entity\Session;
use App\Session\Form\SessionExerciseType;
use App\Session\Service\SessionManager;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SessionCrudController extends BaseCrudController
{
    public const INDEX_VIEW_ARCHIVED = 'archived';
    public const INDEX_VIEW_NOT_ARCHIVED = 'not_archived';

    public const INDEX_VIEWS = [
        self::INDEX_VIEW_ARCHIVED,
        self::INDEX_VIEW_NOT_ARCHIVED,
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly SessionManager $manager
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Session::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->renderContentMaximized();
        $crud->setEntityLabelInSingular('Séance');
        $crud->setEntityLabelInPlural('Séances');
        $crud->setSearchFields(['id', 'name', 'exercises.exercise.name']);

        $crud->overrideTemplate('crud/index', 'session/index.html.twig');

        return $crud;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $rootAlias = $qb->getRootAliases()[0];

        /** @var Request $request */
        $request = $this->requestStack->getMainRequest();
        $view = $request->query->get('view', self::INDEX_VIEW_NOT_ARCHIVED);

        if (\in_array($view, self::INDEX_VIEWS)) {
            $andWhere = \sprintf('%s.isArchived = :is_archived', $rootAlias);
            $qb->andWhere($andWhere);
            $qb->setParameter('is_archived', self::INDEX_VIEW_ARCHIVED === $view);
        }

        return $qb;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        // Favorite batch actions
        if ($this->isView(self::INDEX_VIEW_NOT_ARCHIVED)) {
            $actions->addBatchAction(Action::new('removeFavoriteBatch', 'action.remove_favorite', 'fa fa-star-o')
                ->linkToCrudAction('removeFavoriteBatch'));
            $actions->addBatchAction(Action::new('addFavoriteBach', 'action.add_favorite', 'fa fa-star')
                ->linkToCrudAction('addFavoriteBach'));
        }

        // Duplicate action
        $actions->add(Crud::PAGE_INDEX, Action::new('duplicate', 'action.duplicate', 'fa fa-copy')
            ->linkToCrudAction('duplicate'));

        // Archive batch action
        if ($this->isView(self::INDEX_VIEW_NOT_ARCHIVED)) {
            $actions->addBatchAction(Action::new('archiveBatch', 'action.archive', 'fa fa-folder-plus')
                ->linkToCrudAction('archiveBatch'));
        } elseif ($this->isView(self::INDEX_VIEW_ARCHIVED)) {
            $actions->addBatchAction(Action::new('unarchiveBatch', 'action.unarchive', 'fa fa-folder-minus')
                ->linkToCrudAction('unarchiveBatch'));
        }

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'label.name')
            ->setTemplatePath('field/session/name.html.twig')
            ->hideOnForm();

        yield TextField::new('name', 'label.name')
            ->hideOnIndex()
            ->setFormTypeOption('required', false);

        yield TextareaField::new('description', 'label.description')
            ->hideOnIndex();

        yield CollectionField::new('exercises', 'label.exercises')
            ->hideOnIndex()
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(SessionExerciseType::class);
    }

    // Custom actions controllers

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addFavoriteBach(BatchActionDto $batchActionDto): Response
    {
        return $this->handleFavoriteBatchAction($batchActionDto, true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function removeFavoriteBatch(BatchActionDto $batchActionDto): Response
    {
        return $this->handleFavoriteBatchAction($batchActionDto, false);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function handleFavoriteBatchAction(BatchActionDto $batchActionDto, bool $favorite): Response
    {
        foreach ($batchActionDto->getEntityIds() as $id) {
            /** @var Session $session */
            $session = $this->findOrThrow(Session::class, $id);
            $session->isFavorite = $favorite;
        }

        $this->em()->flush();

        $targetUrl = $this->generateEaUrl(self::class, Crud::PAGE_INDEX, ['view' => self::INDEX_VIEW_NOT_ARCHIVED]);

        return $this->redirect($targetUrl);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function duplicate(): Response
    {
        /** @var Session $session */
        $session = $this->getEntityInstance();
        $this->throwIfNull($session);

        $s = $this->manager->duplicate($session);
        $this->persistAndFlush($s);

        $targetUrl = $this->generateEaUrl(self::class, Crud::PAGE_EDIT, ['entityId' => $s->id]);

        return $this->redirect($targetUrl);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function archiveBatch(BatchActionDto $batchActionDto): Response
    {
        return $this->handleArchiveBatchAction($batchActionDto, true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function unarchiveBatch(BatchActionDto $batchActionDto): Response
    {
        return $this->handleArchiveBatchAction($batchActionDto, false);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function handleArchiveBatchAction(BatchActionDto $batchActionDto, bool $toArchive): Response
    {
        foreach ($batchActionDto->getEntityIds() as $id) {
            /** @var Session $session */
            $session = $this->findOrThrow(Session::class, $id);
            $this->manager->archive($session, $toArchive);
        }

        $this->em()->flush();

        $targetUrl = $this->generateEaUrl(self::class, Crud::PAGE_INDEX, [
            'view' => $toArchive ? self::INDEX_VIEW_NOT_ARCHIVED : self::INDEX_VIEW_ARCHIVED,
        ]);

        return $this->redirect($targetUrl);
    }

    private function isView(string $view): bool
    {
        /** @var Request $request */
        $request = $this->requestStack->getMainRequest();

        return $view === $request->query->get('view');
    }
}
