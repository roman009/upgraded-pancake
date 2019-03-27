<?php

namespace App\Repository;

use App\Entity\GoogleCalendarEventAttendee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GoogleCalendarEventAttendee|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleCalendarEventAttendee|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleCalendarEventAttendee[]    findAll()
 * @method GoogleCalendarEventAttendee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoogleCalendarEventAttendeeRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GoogleCalendarEventAttendee::class);
    }
}
