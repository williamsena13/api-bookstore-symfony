<?php

namespace App\Command;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Editora;
use App\Entity\Livro;
use App\Repository\AssuntoRepository;
use App\Repository\AutorRepository;
use App\Repository\EditoraRepository;
use App\Repository\LivroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-livros',
    description: 'Importa livros da Open Library API (openlibrary.org)',
)]
class ImportLivrosCommand extends Command
{
    private const API_SEARCH = 'https://openlibrary.org/search.json';
    private const FIELDS = 'key,title,author_name,author_key,publisher,first_publish_year,subject,edition_count,isbn';

    private const RANDOM_SEEDS = [
        'literature', 'science', 'history', 'philosophy', 'poetry',
        'adventure', 'mystery', 'romance', 'fantasy', 'biography',
        'psychology', 'economics', 'physics', 'mathematics', 'art',
        'music', 'cooking', 'travel', 'religion', 'politics',
        'technology', 'programming', 'design', 'architecture', 'medicine',
    ];

    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $em,
        private LivroRepository $livroRepo,
        private AutorRepository $autorRepo,
        private EditoraRepository $editoraRepo,
        private AssuntoRepository $assuntoRepo,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('busca', InputArgument::OPTIONAL, 'Termo de busca', '')
            ->addOption('tipo', 't', InputOption::VALUE_OPTIONAL, 'Tipo: geral, titulo, autor, assunto, isbn, aleatorio', 'geral')
            ->addOption('limite', 'l', InputOption::VALUE_OPTIONAL, 'Quantidade máxima', 10)
            ->addOption('idioma', 'i', InputOption::VALUE_OPTIONAL, 'Filtrar por idioma (por, eng, spa)', null)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simula sem salvar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $busca = $input->getArgument('busca');
        $tipo = $input->getOption('tipo');
        $limite = (int) $input->getOption('limite');
        $idioma = $input->getOption('idioma');
        $dryRun = $input->getOption('dry-run');

        if ($tipo !== 'aleatorio' && empty($busca)) {
            $io->error('O argumento "busca" é obrigatório para o tipo: ' . $tipo);
            return Command::FAILURE;
        }

        $io->title('Importando livros da Open Library');

        if ($tipo === 'aleatorio') {
            return $this->importarAleatorio($io, $output, $limite, $idioma, $dryRun);
        }

        $params = $this->buildQuery($busca, $tipo, $limite, $idioma);
        $docs = $this->fetchDocs($io, $params);
        if ($docs === null) return Command::FAILURE;
        if (empty($docs)) return Command::SUCCESS;

        $result = $this->processarDocs($io, $output, $docs, $dryRun);
        $this->exibirResumo($io, $result, $dryRun);

        return Command::SUCCESS;
    }

    private function importarAleatorio(SymfonyStyle $io, OutputInterface $output, int $limite, ?string $idioma, bool $dryRun): int
    {
        $seeds = self::RANDOM_SEEDS;
        shuffle($seeds);
        $porSeed = max(2, (int) ceil($limite / 5));
        $seedsUsados = array_slice($seeds, 0, (int) ceil($limite / $porSeed));

        $io->text('Assuntos sorteados: ' . implode(', ', $seedsUsados));

        $allDocs = [];
        foreach ($seedsUsados as $seed) {
            $io->text("  → Buscando: {$seed}...");
            $params = $this->buildQuery($seed, 'assunto', $porSeed, $idioma);
            $params['offset'] = rand(0, 50);

            try {
                $response = $this->httpClient->request('GET', self::API_SEARCH, ['query' => $params]);
                $docs = $response->toArray()['docs'] ?? [];
                shuffle($docs);
                $allDocs = array_merge($allDocs, $docs);
            } catch (\Exception $e) {
                $io->text("    Erro: {$e->getMessage()}");
            }
        }

        shuffle($allDocs);
        $allDocs = array_slice($allDocs, 0, $limite);

        if (empty($allDocs)) {
            $io->warning('Nenhum livro encontrado.');
            return Command::SUCCESS;
        }

        $result = $this->processarDocs($io, $output, $allDocs, $dryRun);
        $this->exibirResumo($io, $result, $dryRun);

        return Command::SUCCESS;
    }

    private function fetchDocs(SymfonyStyle $io, array $params): ?array
    {
        $io->section('Consultando API...');
        try {
            $response = $this->httpClient->request('GET', self::API_SEARCH, ['query' => $params]);
            $data = $response->toArray();
        } catch (\Exception $e) {
            $io->error('Erro ao consultar API: ' . $e->getMessage());
            return null;
        }

        $docs = $data['docs'] ?? [];
        if (empty($docs)) {
            $io->warning('Nenhum livro encontrado.');
            return [];
        }

        $io->text("Encontrados: {$data['numFound']} | Processando: " . count($docs));
        return $docs;
    }

    private function processarDocs(SymfonyStyle $io, OutputInterface $output, array $docs, bool $dryRun): array
    {
        $progress = new ProgressBar($output, count($docs));
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $progress->setMessage('Iniciando...');
        $progress->start();

        $stats = ['importados' => 0, 'ignorados' => 0, 'erros' => 0];
        $livrosImportados = [];

        foreach ($docs as $doc) {
            $titulo = mb_substr($doc['title'] ?? 'Sem título', 0, 255);
            $progress->setMessage(mb_substr($titulo, 0, 40));

            try {
                if ($this->livroRepo->findOneBy(['titulo' => $titulo])) {
                    $stats['ignorados']++;
                    $progress->advance();
                    continue;
                }

                $editora = $this->findOrCreateEditora($doc);
                $autores = $this->processarAutores($doc);
                $assuntos = $this->processarAssuntos($doc);

                $livro = new Livro();
                $livro->setTitulo($titulo);
                $livro->setEditora($editora);
                $livro->setEdicao(min($doc['edition_count'] ?? 1, 999));
                $livro->setAnoPublicacao($this->extrairAno($doc));
                $livro->setPreco(number_format(rand(1990, 9990) / 100, 2, '.', ''));

                if (isset($doc['isbn'][0])) {
                    $livro->setIsbn(mb_substr($doc['isbn'][0], 0, 20));
                }

                foreach ($autores as $autor) { $livro->addAutor($autor); }
                foreach ($assuntos as $assunto) { $livro->addAssunto($assunto); }

                if (!$dryRun) {
                    $this->em->persist($livro);
                }

                $livrosImportados[] = [
                    mb_substr($titulo, 0, 40),
                    $editora->getNome(),
                    implode(', ', array_map(fn($a) => $a->getNome(), $autores)),
                    $livro->getAnoPublicacao(),
                    'R$ ' . number_format((float) $livro->getPreco(), 2, ',', '.'),
                ];
                $stats['importados']++;
            } catch (\Throwable $e) {
                $stats['erros']++;
                $msg = sprintf('[ERRO] "%s" — %s em %s:%d', $titulo, $e->getMessage(), basename($e->getFile()), $e->getLine());
                $io->writeln("\n  <error>{$msg}</error>");
                $this->logger->error('import-livros: ' . $msg, ['exception' => $e]);
            }

            $progress->advance();
        }

        if (!$dryRun) {
            try {
                $this->em->flush();
            } catch (\Throwable $e) {
                $msg = 'Erro no flush: ' . $e->getMessage();
                $io->error($msg);
                $this->logger->error('import-livros flush: ' . $msg, ['exception' => $e]);
            }
        }

        $progress->finish();
        $output->writeln('');

        return ['stats' => $stats, 'livros' => $livrosImportados];
    }

    private function exibirResumo(SymfonyStyle $io, array $result, bool $dryRun): void
    {
        if ($result['livros']) {
            $io->section('Livros importados');
            $io->table(['Título', 'Editora', 'Autores', 'Ano', 'Preço'], $result['livros']);
        }

        $io->success($dryRun ? 'Simulação concluída!' : 'Importação concluída!');
        $io->table(
            ['Métrica', 'Qtd'],
            [
                ['Importados', $result['stats']['importados']],
                ['Ignorados (duplicatas)', $result['stats']['ignorados']],
                ['Erros', $result['stats']['erros']],
                ['Total processado', array_sum($result['stats'])],
            ]
        );
    }

    private function buildQuery(string $busca, string $tipo, int $limite, ?string $idioma): array
    {
        $params = ['limit' => $limite, 'fields' => self::FIELDS];

        match ($tipo) {
            'titulo'  => $params['title'] = $busca,
            'autor'   => $params['author'] = $busca,
            'assunto' => $params['subject'] = $busca,
            'isbn'    => $params['isbn'] = preg_replace('/[^0-9X]/', '', strtoupper($busca)),
            default   => $params['q'] = $busca,
        };

        if ($idioma) {
            $params['language'] = $idioma;
        }

        return $params;
    }

    private function extrairAno(array $doc): ?int
    {
        $ano = $doc['first_publish_year'] ?? null;
        if ($ano && $ano > 0 && $ano <= (int) date('Y')) {
            return (int) $ano;
        }
        return null;
    }

    private function findOrCreateEditora(array $doc): Editora
    {
        $nome = mb_substr($doc['publisher'][0] ?? 'Editora Desconhecida', 0, 255);
        $editora = $this->editoraRepo->findOneBy(['nome' => $nome]);
        if ($editora) return $editora;

        $editora = new Editora();
        $editora->setNome($nome);
        $this->em->persist($editora);

        return $editora;
    }

    private function processarAutores(array $doc): array
    {
        $autores = [];
        $nomes = $doc['author_name'] ?? ['Autor Desconhecido'];

        foreach (array_slice($nomes, 0, 5) as $nome) {
            $nome = mb_substr($nome, 0, 255);
            $autor = $this->autorRepo->findOneBy(['nome' => $nome]);

            if (!$autor) {
                $autor = new Autor();
                $autor->setNome($nome);
                $this->em->persist($autor);
            }

            $autores[] = $autor;
        }

        return $autores;
    }

    private function processarAssuntos(array $doc): array
    {
        $assuntos = [];
        $subjects = array_slice($doc['subject'] ?? [], 0, 3);

        foreach ($subjects as $desc) {
            $desc = mb_substr(trim($desc), 0, 255);
            if (empty($desc)) continue;

            $assunto = $this->assuntoRepo->findOneBy(['descricao' => $desc]);
            if (!$assunto) {
                $assunto = new Assunto();
                $assunto->setDescricao($desc);
                $this->em->persist($assunto);
            }

            $assuntos[] = $assunto;
        }

        return $assuntos;
    }
}
