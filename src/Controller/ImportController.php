<?php

namespace App\Controller;

use App\Entity\ImportLog;
use App\Repository\ImportLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/importar')]
class ImportController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {}

    #[Route('/', name: 'app_import_index')]
    public function index(ImportLogRepository $repo): Response
    {
        return $this->render('import/index.html.twig', [
            'stats' => $repo->getStats(),
        ]);
    }

    #[Route('/livros', name: 'app_import_livros')]
    public function livros(\App\Repository\LivroRepository $repo): Response
    {
        return $this->render('import/livros.html.twig', [
            'livros' => $repo->findAllWithRelations(),
        ]);
    }

    #[Route('/run', name: 'app_import_run', methods: ['POST'])]
    public function run(Request $request): JsonResponse
    {
        try {
            $tipo = $request->request->get('tipo', 'aleatorio');
            $busca = trim($request->request->get('busca', ''));
            $limite = max(1, min((int) $request->request->get('limite', 10), 50));
            $idioma = $request->request->get('idioma', '') ?: null;
            $dryRun = $request->request->getBoolean('dry_run');

            if (!in_array($tipo, ['geral', 'titulo', 'autor', 'assunto', 'isbn', 'aleatorio'])) {
                return new JsonResponse(['success' => false, 'output' => 'Tipo inválido.'], 400);
            }
            if ($tipo !== 'aleatorio' && empty($busca)) {
                return new JsonResponse(['success' => false, 'output' => 'O campo de busca é obrigatório.'], 400);
            }

            $projectDir = $this->getParameter('kernel.project_dir');
            $console = $projectDir . '/bin/console';

            $args = 'app:import-livros ' . escapeshellarg($busca ?: '') . ' -t ' . escapeshellarg($tipo) . ' -l ' . (int)$limite . ' --no-ansi';
            if ($idioma) { $args .= ' -i ' . escapeshellarg($idioma); }
            if ($dryRun) { $args .= ' --dry-run'; }

            $cmd = 'php ' . escapeshellarg($console) . ' ' . $args . ' 2>&1';
            $this->logger->info('import-livros start', ['cmd' => $cmd]);

            $lines = [];
            $exitCode = 0;
            $start = microtime(true);
            exec($cmd, $lines, $exitCode);
            $duration = round(microtime(true) - $start, 2);
            $outputStr = implode("\n", $lines);

            $this->logger->info('import-livros end', ['exitCode' => $exitCode, 'duration' => $duration]);
            if ($exitCode !== 0) {
                $this->logger->error('import-livros failed', ['exitCode' => $exitCode, 'output' => mb_substr($outputStr, 0, 1000)]);
            }

            $importados = $this->parseStat($outputStr, 'Importados');
            $ignorados  = $this->parseStat($outputStr, 'Ignorados');
            $erros      = $this->parseStat($outputStr, 'Erros');

            try {
                $log = (new ImportLog())
                    ->setTipo($tipo)->setBusca($busca ?: null)->setLimite($limite)
                    ->setIdioma($idioma)->setDryRun($dryRun)
                    ->setImportados($importados)->setIgnorados($ignorados)->setErros($erros)
                    ->setSucesso($exitCode === 0)->setExitCode($exitCode)
                    ->setOutput($outputStr)->setUsuario($this->getUser()?->getNome())
                    ->setDuracaoSegundos((string) $duration);
                $this->em->persist($log);
                $this->em->flush();
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao salvar ImportLog: ' . $e->getMessage());
            }

            return new JsonResponse([
                'success' => $exitCode === 0,
                'output'  => $outputStr ?: '(sem output)',
                'exitCode' => $exitCode,
                'stats'   => compact('importados', 'ignorados', 'erros', 'duration'),
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('import-livros exception: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['success' => false, 'output' => '[EXCEÇÃO] ' . $e->getMessage()], 500);
        }
    }

    #[Route('/historico', name: 'app_import_historico')]
    public function historico(ImportLogRepository $repo): Response
    {
        return $this->render('import/historico.html.twig', [
            'logs' => $repo->findBy([], ['createdAt' => 'DESC']),
            'stats' => $repo->getStats(),
            'statsByTipo' => $repo->getStatsByTipo(),
        ]);
    }

    #[Route('/historico/{id}', name: 'app_import_detalhe')]
    public function detalhe(ImportLog $log): Response
    {
        return $this->render('import/detalhe.html.twig', ['log' => $log]);
    }

    private function parseStat(string $output, string $label): int
    {
        if (preg_match('/' . preg_quote($label, '/') . '[\s|]+(\d+)/', $output, $m)) {
            return (int) $m[1];
        }
        return 0;
    }
}
