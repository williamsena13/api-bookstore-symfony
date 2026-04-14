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
