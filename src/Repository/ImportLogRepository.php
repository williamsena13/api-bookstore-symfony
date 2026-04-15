<?php

namespace App\Repository;

use App\Entity\ImportLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ImportLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportLog::class);
    }

    public function getStats(): array
    {
        return $this->getEntityManager()->getConnection()->fetchAssociative(
            'SELECT
                COUNT(*) as total_execucoes,
                COALESCE(SUM(importados), 0) as total_importados,
                COALESCE(SUM(ignorados), 0) as total_ignorados,
                COALESCE(SUM(erros), 0) as total_erros,
                SUM(CASE WHEN sucesso = 1 THEN 1 ELSE 0 END) as execucoes_sucesso,
                SUM(CASE WHEN sucesso = 0 THEN 1 ELSE 0 END) as execucoes_falha,
                ROUND(AVG(duracao_segundos), 2) as media_duracao,
                MAX(created_at) as ultima_execucao
            FROM import_log WHERE dry_run = 0'
        ) ?: [];
    }

    public function getStatsByTipo(): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT tipo, COUNT(*) as qtd, SUM(importados) as importados, SUM(ignorados) as ignorados, SUM(erros) as erros
            FROM import_log WHERE dry_run = 0 GROUP BY tipo ORDER BY importados DESC'
        );
    }
}
