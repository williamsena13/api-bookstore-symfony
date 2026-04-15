<?php

namespace App\Repository;

use App\Entity\Livro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livro::class);
    }

    public function findAllWithRelations(): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT l.id, l.titulo, l.isbn, l.ano_publicacao, l.edicao, l.preco, l.created_at,
                    e.nome AS editora,
                    GROUP_CONCAT(DISTINCT a.nome ORDER BY a.nome SEPARATOR ", ") AS autores,
                    GROUP_CONCAT(DISTINCT s.descricao ORDER BY s.descricao SEPARATOR ", ") AS assuntos
               FROM livro l
          LEFT JOIN editora e ON e.id = l.editora_id
          LEFT JOIN livro_autor la ON la.livro_id = l.id
          LEFT JOIN autor a ON a.id = la.autor_id
          LEFT JOIN livro_assunto ls ON ls.livro_id = l.id
          LEFT JOIN assunto s ON s.id = ls.assunto_id
           GROUP BY l.id, l.titulo, l.isbn, l.ano_publicacao, l.edicao, l.preco, l.created_at, e.nome
           ORDER BY l.created_at DESC'
        );
    }

    public function findPaginated(int $page, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $conn = $this->getEntityManager()->getConnection();

        $total = (int) $conn->fetchOne('SELECT COUNT(*) FROM livro');

        $rows = $conn->fetchAllAssociative(
            'SELECT l.id, l.titulo, l.isbn, l.ano_publicacao, l.edicao, l.preco,
                    e.nome AS editora,
                    GROUP_CONCAT(DISTINCT a.nome ORDER BY a.nome SEPARATOR ", ") AS autores
               FROM livro l
          LEFT JOIN editora e ON e.id = l.editora_id
          LEFT JOIN livro_autor la ON la.livro_id = l.id
          LEFT JOIN autor a ON a.id = la.autor_id
           GROUP BY l.id, l.titulo, l.isbn, l.ano_publicacao, l.edicao, l.preco, e.nome
           ORDER BY l.id DESC
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

    public function findRelatorioPorAutor(): array
    {
        return $this->getEntityManager()->getConnection()
            ->fetchAllAssociative('SELECT * FROM vw_relatorio_livros_por_autor');
    }

    public function findRelatorioPorEditora(): array
    {
        return $this->getEntityManager()->getConnection()
            ->fetchAllAssociative('SELECT * FROM vw_relatorio_livros_por_editora');
    }

    public function findRelatorioPorAssunto(): array
    {
        return $this->getEntityManager()->getConnection()
            ->fetchAllAssociative('SELECT * FROM vw_relatorio_livros_por_assunto');
    }

    public function findMostExpensive(int $limit = 10): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT l.titulo, e.nome as editora, l.preco
               FROM livro l
          LEFT JOIN editora e ON e.id = l.editora_id
              WHERE l.preco IS NOT NULL
           ORDER BY l.preco DESC
              LIMIT :limit',
            ['limit' => $limit],
            ['limit' => \Doctrine\DBAL\ParameterType::INTEGER]
        );
    }
}
