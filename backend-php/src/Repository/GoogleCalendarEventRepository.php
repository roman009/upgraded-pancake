<?php

namespace App\Repository;

use App\Entity\GoogleCalendarEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GoogleCalendarEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleCalendarEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleCalendarEvent[]    findAll()
 * @method GoogleCalendarEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoogleCalendarEventRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GoogleCalendarEvent::class);
    }

    public function findNextEvents(int $pageSize, int $offset)
    {
        $qb = $this->createQueryBuilder('e');
        $qb
//            ->andWhere('e.start > :startDate')
//            ->setParameter('startDate', new \DateTime)
            ->orderBy('e.start', 'ASC')
            ->setMaxResults($pageSize)
            ->setFirstResult($offset)
        ;

        return $qb->getQuery()->getResult();
    }
}
