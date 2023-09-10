<?php

namespace App\Core\Controller;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BaseCrudController extends AbstractCrudController
{
    public static function getSubscribedServices(): array
    {
        return \array_merge(parent::getSubscribedServices(), [
            'doctrine.orm.default_entity_manager' => EntityManagerInterface::class,
        ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa fa-plus')->setLabel(false);
        });
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->setIcon('fa fa-edit');
        });
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action->setIcon('fa fa-trash');
        });

        return $actions;
    }

    // Services shortcut

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function em(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        return $em;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function adminUrlGenerator(): AdminUrlGenerator
    {
        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $adminUrlGenerator;
    }

    // Helpers

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function generateEaUrl(string $controllerFqcn, string $action, array $options = []): string
    {
        $urlGenerator = $this->adminUrlGenerator()->setController($controllerFqcn)
            ->setAction($action);

        if (isset($options['view'])) {
            $urlGenerator->set('view', $options['view']);
        }

        if (isset($options['entityId'])) {
            $urlGenerator->setEntityId($options['entityId']);
        }

        return $urlGenerator->generateUrl();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function persistAndFlush(object $entity): void
    {
        $this->em()->persist($entity);
        $this->em()->flush();
    }

    public function getEntityInstance(): mixed
    {
        return $this->getContext()?->getEntity()?->getInstance();
    }

    public function throwIfNull(?object $target): void
    {
        if (null === $target) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @param string|class-string $classname
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findOrThrow(string $classname, int $id): mixed
    {
        $entity = $this->em()->find($classname, $id); /* @phpstan-ignore-line */
        if ($entity) {
            return $entity;
        }

        throw new NotFoundHttpException(\sprintf('Entity %s with ID %d not found.', $classname, $id));
    }
}
