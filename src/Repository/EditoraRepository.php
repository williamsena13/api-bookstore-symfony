<?php

namespace App\Repository;

use App\Entity\Editora;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EditoraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Editora::class);
    }

    public function findTopPublishersByBooks(int $limit = 10): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT e.nome, COUNT(l.id) as total
               FROM editora e
               JOIN livro l ON l.editora_id = e.id
           GROUP BY e.id, e.nome
           ORDER BY total DESC
              LIMIT :limit',
            ['limit' => $limit],
            ['limit' => \Doctrine\DBAL\ParameterType::INTEGER]
        );
    }
}
