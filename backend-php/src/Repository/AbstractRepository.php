<?php

namespace App\Repository;

use App\Entity\Attendee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function persistAndFlush(object $object)
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }
}
