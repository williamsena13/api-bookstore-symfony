<?php

namespace App\Tests\Service;

use App\Repository\AssuntoRepository;
use App\Repository\AutorRepository;
use App\Repository\EditoraRepository;
use App\Repository\LivroRepository;
use App\Service\RelatorioService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class RelatorioServiceTest extends TestCase
{
    private RelatorioService $service;
    private LivroRepository $livroRepo;
    private AutorRepository $autorRepo;
    private EditoraRepository $editoraRepo;
    private AssuntoRepository $assuntoRepo;

    protected function setUp(): void
    {
        $this->livroRepo   = $this->createMock(LivroRepository::class);
        $this->autorRepo   = $this->createMock(AutorRepository::class);
        $this->editoraRepo = $this->createMock(EditoraRepository::class);
        $this->assuntoRepo = $this->createMock(AssuntoRepository::class);
        $logger            = $this->createMock(LoggerInterface::class);

        $this->service = new RelatorioService(
            $this->livroRepo,
            $this->autorRepo,
            $this->editoraRepo,
            $this->assuntoRepo,
            $logger,
        );
    }

    public function testGetLivrosPorAutorAgrupaCorretamente(): void
    {
        $this->livroRepo->method('findRelatorioPorAutor')->willReturn([
            ['autor_nome' => 'Machado de Assis', 'livro_titulo' => 'Dom Casmurro'],
            ['autor_nome' => 'Machado de Assis', 'livro_titulo' => 'Memórias Póstumas'],
            ['autor_nome' => 'Clarice Lispector', 'livro_titulo' => 'A Hora da Estrela'],
        ]);

        $resultado = $this->service->getLivrosPorAutor();

        $this->assertArrayHasKey('Machado de Assis', $resultado);
        $this->assertArrayHasKey('Clarice Lispector', $resultado);
        $this->assertCount(2, $resultado['Machado de Assis']);
        $this->assertCount(1, $resultado['Clarice Lispector']);
    }

    public function testGetLivrosPorAutorRetornaVazioSemDados(): void
    {
        $this->livroRepo->method('findRelatorioPorAutor')->willReturn([]);
        $this->assertSame([], $this->service->getLivrosPorAutor());
    }

    public function testGetLivrosPorEditoraAgrupaCorretamente(): void
    {
        $this->livroRepo->method('findRelatorioPorEditora')->willReturn([
            ['editora_nome' => 'Companhia das Letras', 'livro_titulo' => 'Dom Casmurro'],
            ['editora_nome' => 'Rocco', 'livro_titulo' => 'A Hora da Estrela'],
            ['editora_nome' => 'Rocco', 'livro_titulo' => 'Perto do Coração Selvagem'],
        ]);

        $resultado = $this->service->getLivrosPorEditora();

        $this->assertArrayHasKey('Companhia das Letras', $resultado);
        $this->assertArrayHasKey('Rocco', $resultado);
        $this->assertCount(1, $resultado['Companhia das Letras']);
        $this->assertCount(2, $resultado['Rocco']);
    }

    public function testGetLivrosPorAssuntoAgrupaCorretamente(): void
    {
        $this->livroRepo->method('findRelatorioPorAssunto')->willReturn([
            ['assunto' => 'Romance', 'livro_titulo' => 'Dom Casmurro'],
            ['assunto' => 'Romance', 'livro_titulo' => 'A Hora da Estrela'],
            ['assunto' => 'Ficção', 'livro_titulo' => 'Duna'],
        ]);

        $resultado = $this->service->getLivrosPorAssunto();

        $this->assertArrayHasKey('Romance', $resultado);
        $this->assertArrayHasKey('Ficção', $resultado);
        $this->assertCount(2, $resultado['Romance']);
        $this->assertCount(1, $resultado['Ficção']);
    }

    public function testGetRankingDataRetornaEstruturaCerta(): void
    {
        $this->autorRepo->method('findTopAuthorsByBooks')->willReturn([
            ['nome' => 'Machado de Assis', 'total' => 5],
        ]);
        $this->editoraRepo->method('findTopPublishersByBooks')->willReturn([
            ['nome' => 'Companhia das Letras', 'total' => 10],
        ]);
        $this->assuntoRepo->method('findTopSubjectsByBooks')->willReturn([
            ['descricao' => 'Romance', 'total' => 8],
        ]);
        $this->livroRepo->method('findMostExpensive')->willReturn([
            ['titulo' => 'Duna', 'preco' => '199.90'],
        ]);

        $ranking = $this->service->getRankingData();

        $this->assertArrayHasKey('autorMaisLivros', $ranking);
        $this->assertArrayHasKey('editoraMaisLivros', $ranking);
        $this->assertArrayHasKey('assuntoMaisLivros', $ranking);
        $this->assertArrayHasKey('livroMaisCaro', $ranking);
        $this->assertSame('Machado de Assis', $ranking['autorMaisLivros'][0]['nome']);
        $this->assertSame('Duna', $ranking['livroMaisCaro'][0]['titulo']);
    }

    public function testGetLivrosPorAutorLancaExcecaoEmErro(): void
    {
        $this->livroRepo->method('findRelatorioPorAutor')
            ->willThrowException(new \RuntimeException('DB error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Não foi possível gerar o relatório por autor.');

        $this->service->getLivrosPorAutor();
    }

    public function testGetRankingDataLancaExcecaoEmErro(): void
    {
        $this->autorRepo->method('findTopAuthorsByBooks')
            ->willThrowException(new \RuntimeException('DB error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Não foi possível gerar os rankings.');

        $this->service->getRankingData();
    }
}
