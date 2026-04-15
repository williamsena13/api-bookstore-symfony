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

    public function findAllWithCount(): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT s.id, s.descricao, s.created_at,
                    COUNT(ls.livro_id) as total_livros
               FROM assunto s
          LEFT JOIN livro_assunto ls ON ls.assunto_id = s.id
           GROUP BY s.id, s.descricao, s.created_at
           ORDER BY s.descricao'
        );
    }

    public function findPaginated(int $page, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $conn = $this->getEntityManager()->getConnection();

        $total = (int) $conn->fetchOne('SELECT COUNT(*) FROM assunto');

        $rows = $conn->fetchAllAssociative(
            'SELECT s.id, s.descricao, s.created_at, COUNT(ls.livro_id) as total_livros
               FROM assunto s
          LEFT JOIN livro_assunto ls ON ls.assunto_id = s.id
           GROUP BY s.id, s.descricao, s.created_at
           ORDER BY s.descricao
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

    public function findLivrosByAssunto(int $id): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT l.titulo, l.isbn, l.ano_publicacao, l.preco, e.nome AS editora
               FROM livro l
          LEFT JOIN editora e ON e.id = l.editora_id
               JOIN livro_assunto ls ON ls.livro_id = l.id
              WHERE ls.assunto_id = :id
           ORDER BY l.titulo',
            ['id' => $id]
        );
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
