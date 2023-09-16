<?php

namespace App\Core\Controller;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class BaseViewCrudController extends BaseCrudController
{
    public const VIEW_ARCHIVED = 'archived';
    public const VIEW_NOT_ARCHIVED = 'not_archived';

    public const ARCHIVE_VIEWS = [
        self::VIEW_ARCHIVED,
        self::VIEW_NOT_ARCHIVED,
    ];

    public const VIEWS = [
        self::VIEW_ARCHIVED,
        self::VIEW_NOT_ARCHIVED,
    ];

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $rootAlias = $qb->getRootAliases()[0];

        /** @var Request $request */
        $request = $this->requestStack->getMainRequest();
        $view = $request->query->get('view');

        if (!\in_array($view, self::VIEWS)) {
            throw new \UnexpectedValueException(\sprintf('View "%s" given. Expected "%s" views.', $view, \implode(', ', self::VIEWS)));
        }

        // Archive's views
        if (\in_array($view, self::ARCHIVE_VIEWS)) {
            $andWhere = \sprintf('%s.isArchived = :is_archived', $rootAlias);
            $qb->andWhere($andWhere);
            $qb->setParameter('is_archived', self::VIEW_ARCHIVED === $view);
        }

        return $qb;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        // Archive batch action
        if ($this->isView(self::VIEW_NOT_ARCHIVED)) {
            $actions->addBatchAction(Action::new('archiveBatch', 'action.archive', 'fa fa-folder-plus')
                ->linkToCrudAction('archiveBatch'));
        } elseif ($this->isView(self::VIEW_ARCHIVED)) {
            $actions->addBatchAction(Action::new('unarchiveBatch', 'action.unarchive', 'fa fa-folder-minus')
                ->linkToCrudAction('unarchiveBatch'));
        }

        return $actions;
    }

    protected function isView(string $view): bool
    {
        if (!\in_array($view, self::VIEWS)) {
            throw new \UnexpectedValueException(\sprintf('View "%s" given. Expected "%s" views.', $view, \implode(', ', self::VIEWS)));
        }

        /** @var Request $request */
        $request = $this->requestStack->getMainRequest();

        return $view === $request->query->get('view');
    }
}
