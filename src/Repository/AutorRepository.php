<?php

namespace App\Repository;

use App\Entity\Autor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Autor::class);
    }

    public function findAllWithCount(): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT a.id, a.nome, a.created_at,
                    COUNT(la.livro_id) as total_livros
               FROM autor a
          LEFT JOIN livro_autor la ON la.autor_id = a.id
           GROUP BY a.id, a.nome, a.created_at
           ORDER BY a.nome'
        );
    }

    public function findPaginated(int $page, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $conn = $this->getEntityManager()->getConnection();

        $total = (int) $conn->fetchOne('SELECT COUNT(*) FROM autor');

        $rows = $conn->fetchAllAssociative(
            'SELECT a.id, a.nome, a.created_at, COUNT(la.livro_id) as total_livros
               FROM autor a
          LEFT JOIN livro_autor la ON la.autor_id = a.id
           GROUP BY a.id, a.nome, a.created_at
           ORDER BY a.nome
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

    public function findLivrosByAutor(int $id): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT l.titulo, l.isbn, l.ano_publicacao, l.preco, e.nome AS editora
               FROM livro l
          LEFT JOIN editora e ON e.id = l.editora_id
               JOIN livro_autor la ON la.livro_id = l.id
              WHERE la.autor_id = :id
           ORDER BY l.titulo',
            ['id' => $id]
        );
    }

    public function findTopAuthorsByBooks(int $limit = 10): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT a.nome, COUNT(la.livro_id) as total
               FROM autor a
               JOIN livro_autor la ON la.autor_id = a.id
           GROUP BY a.id, a.nome
           ORDER BY total DESC
              LIMIT :limit',
            ['limit' => $limit],
            ['limit' => \Doctrine\DBAL\ParameterType::INTEGER]
        );
    }
}
