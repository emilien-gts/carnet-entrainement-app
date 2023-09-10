<?php

namespace App\Core\Trait\Controller;

use App\Core\Contracts\ArchiveAwareInterface;
use App\Core\Controller\BaseViewCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

trait ArchiveBatchActionTrait
{
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
        $fqcn = $batchActionDto->getEntityFqcn();
        foreach ($batchActionDto->getEntityIds() as $id) {
            $entity = $this->findOrThrow($fqcn, $id);
            if ($entity instanceof ArchiveAwareInterface) {
                $entity->setIsArchived($toArchive);
            }
        }

        $this->em()->flush();

        $targetUrl = $this->generateEaUrl(self::class, Crud::PAGE_INDEX, [
            'view' => $toArchive ? BaseViewCrudController::VIEW_NOT_ARCHIVED : BaseViewCrudController::VIEW_ARCHIVED,
        ]);

        return $this->redirect($targetUrl);
    }
}
