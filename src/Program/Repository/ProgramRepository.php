<?php

namespace App\Program\Repository;

use App\Core\Utils;
use App\Program\Entity\Program;
use App\Session\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

    /**
     * @return Program[]
     */
    public function findBySession(Session $session): array
    {
        $dayOfWeeks = Utils::day_of_weeks();
        $qb = $this->createQueryBuilder('e');

        $andWhere = '';
        foreach ($dayOfWeeks as $key => $dayOfWeek) {
            $andWhere .= \sprintf('e.%s = :session', \strtolower($dayOfWeek));
            if ($key !== array_key_last($dayOfWeeks)) {
                $andWhere .= ' OR ';
            }
        }

        $qb->andWhere($andWhere);
        $qb->setParameter('session', $session);

        /* @phpstan-ignore-next-line */
        return $qb->getQuery()->getResult();
    }
}
