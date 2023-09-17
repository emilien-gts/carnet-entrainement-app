<?php

namespace App\Session\Controller;

use App\Core\Controller\BaseViewCrudController;
use App\Core\Trait\Controller\ArchiveBatchActionTrait;
use App\Core\Trait\Controller\FavoriteBatchActionTrait;
use App\Session\Entity\Session;
use App\Session\Form\SessionExerciseType;
use App\Session\Service\SessionManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class SessionCrudController extends BaseViewCrudController
{
    use FavoriteBatchActionTrait;
    use ArchiveBatchActionTrait;

    public function __construct(RequestStack $requestStack, private readonly SessionManager $manager)
    {
        parent::__construct($requestStack);
    }

    public static function getEntityFqcn(): string
    {
        return Session::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->renderContentMaximized();
        $crud->setEntityLabelInSingular('label.session');
        $crud->setEntityLabelInPlural('label.sessions');
        $crud->setSearchFields(['id', 'name', 'description', 'exercises.exercise.name']);

        $crud->overrideTemplate('crud/index', 'session/index.html.twig');

        $crud->setDefaultSort(['name' => 'ASC']);

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

    /**
     * @param Session $entityInstance
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($this->manager->isLinkToProgram($entityInstance)) {
            $this->addFlash('danger', \sprintf('Impossible de supprimer la séance "%s", la séance a été archivée', $entityInstance));
            $entityInstance->setIsArchived(true);
            $this->persistAndFlush($entityInstance);

            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
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

    /**
     * @param Session $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $version = $this->manager->createVersion($entityInstance);
        if ($version) {
            $entityManager->persist($version);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * @param Session $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $version = $this->manager->createVersion($entityInstance);
        if ($version) {
            $entityManager->persist($version);
        }

        parent::updateEntity($entityManager, $entityInstance);
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
}
