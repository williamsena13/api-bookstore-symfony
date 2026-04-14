<?php

namespace App\Service;

use App\Repository\AssuntoRepository;
use App\Repository\AutorRepository;
use App\Repository\EditoraRepository;
use App\Repository\LivroRepository;
use Psr\Log\LoggerInterface;

class RelatorioService
{
    public function __construct(
        private LivroRepository $livroRepository,
        private AutorRepository $autorRepository,
        private EditoraRepository $editoraRepository,
        private AssuntoRepository $assuntoRepository,
        private LoggerInterface $logger,
    ) {}

    public function getLivrosPorAutor(): array
    {
        try {
            return $this->agrupar($this->livroRepository->findRelatorioPorAutor(), 'autor_nome');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao gerar relatório por autor: ' . $e->getMessage());
            throw new \RuntimeException('Não foi possível gerar o relatório por autor.');
        }
    }

    public function getLivrosPorEditora(): array
    {
        try {
            return $this->agrupar($this->livroRepository->findRelatorioPorEditora(), 'editora_nome');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao gerar relatório por editora: ' . $e->getMessage());
            throw new \RuntimeException('Não foi possível gerar o relatório por editora.');
        }
    }

    public function getLivrosPorAssunto(): array
    {
        try {
            return $this->agrupar($this->livroRepository->findRelatorioPorAssunto(), 'assunto');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao gerar relatório por assunto: ' . $e->getMessage());
            throw new \RuntimeException('Não foi possível gerar o relatório por assunto.');
        }
    }

    public function getRankingData(): array
    {
        try {
            return [
                'autorMaisLivros' => $this->autorRepository->findTopAuthorsByBooks(),
                'editoraMaisLivros' => $this->editoraRepository->findTopPublishersByBooks(),
                'assuntoMaisLivros' => $this->assuntoRepository->findTopSubjectsByBooks(),
                'livroMaisCaro' => $this->livroRepository->findMostExpensive(),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao gerar ranking: ' . $e->getMessage());
            throw new \RuntimeException('Não foi possível gerar os rankings.');
        }
    }

    private function agrupar(array $dados, string $chave): array
    {
        $agrupado = [];
        foreach ($dados as $r) {
            $agrupado[$r[$chave]][] = $r;
        }
        return $agrupado;
    }
}
