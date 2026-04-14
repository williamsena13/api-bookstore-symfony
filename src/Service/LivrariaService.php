<?php

namespace App\Service;

use App\Entity\Livraria;
use App\Repository\LivrariaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class LivrariaService
{
    public function __construct(
        private LivrariaRepository $repository,
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {}

    public function getCurrent(): Livraria
    {
        return $this->repository->findCurrent() ?? new Livraria();
    }

    public function save(Livraria $livraria): void
    {
        try {
            $livraria->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($livraria);
            $this->em->flush();
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao salvar livraria: ' . $e->getMessage());
            throw new \RuntimeException('Erro ao salvar os dados da livraria.');
        }
    }
}
