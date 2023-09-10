<?php

namespace App\Core\Trait\Controller;

use App\Core\Contracts\FavoriteAwareInterface;
use App\Core\Controller\BaseViewCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

trait FavoriteBatchActionTrait
{
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
        $fqcn = $batchActionDto->getEntityFqcn();
        foreach ($batchActionDto->getEntityIds() as $id) {
            $entity = $this->findOrThrow($fqcn, $id);
            if ($entity instanceof FavoriteAwareInterface) {
                $entity->setIsFavorite($favorite);
            }
        }

        $this->em()->flush();

        $targetUrl = $this->generateEaUrl(self::class, Crud::PAGE_INDEX, ['view' => BaseViewCrudController::VIEW_NOT_ARCHIVED]);

        return $this->redirect($targetUrl);
    }
}
