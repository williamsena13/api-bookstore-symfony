<?php

namespace App\Repository;

use App\Entity\Livraria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivrariaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livraria::class);
    }

    public function findCurrent(): ?Livraria
    {
        return $this->findOneBy([], ['id' => 'ASC']);
    }
}
