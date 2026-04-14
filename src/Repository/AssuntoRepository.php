<?php

namespace App\Repository;

use App\Entity\Assunto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AssuntoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assunto::class);
    }

    public function findTopSubjectsByBooks(int $limit = 10): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT s.descricao, COUNT(ls.livro_id) as total
               FROM assunto s
               JOIN livro_assunto ls ON ls.assunto_id = s.id
           GROUP BY s.id, s.descricao
           ORDER BY total DESC
              LIMIT :limit',
            ['limit' => $limit],
            ['limit' => \Doctrine\DBAL\ParameterType::INTEGER]
        );
    }
}
