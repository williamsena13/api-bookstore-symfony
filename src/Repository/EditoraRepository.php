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

    public function findAllWithCount(): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT e.id, e.nome, e.created_at,
                    COUNT(l.id) as total_livros
               FROM editora e
          LEFT JOIN livro l ON l.editora_id = e.id
           GROUP BY e.id, e.nome, e.created_at
           ORDER BY e.nome'
        );
    }

    public function findPaginated(int $page, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $conn = $this->getEntityManager()->getConnection();

        $total = (int) $conn->fetchOne('SELECT COUNT(*) FROM editora');

        $rows = $conn->fetchAllAssociative(
            'SELECT e.id, e.nome, e.created_at, COUNT(l.id) as total_livros
               FROM editora e
          LEFT JOIN livro l ON l.editora_id = e.id
           GROUP BY e.id, e.nome, e.created_at
           ORDER BY e.nome
              LIMIT :limit OFFSET :offset',
            ['limit' => $perPage, 'offset' => $offset],
            ['limit' => \Doctrine\DBAL\ParameterType::INTEGER, 'offset' => \Doctrine\DBAL\ParameterType::INTEGER]
        );

        return [
            'items'      => $rows,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    public function findLivrosByEditora(int $id): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT l.titulo, l.isbn, l.ano_publicacao, l.edicao, l.preco
               FROM livro l
              WHERE l.editora_id = :id
           ORDER BY l.titulo',
            ['id' => $id]
        );
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
