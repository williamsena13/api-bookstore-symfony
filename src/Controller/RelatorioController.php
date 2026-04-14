<?php

namespace App\Controller;

use App\Service\PdfService;
use App\Service\RelatorioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/relatorios')]
class RelatorioController extends AbstractController
{
    public function __construct(
        private RelatorioService $relatorioService,
        private PdfService $pdfService,
    ) {}

    #[Route('/', name: 'app_relatorio_index')]
    public function index(): Response
    {
        return $this->render('relatorio/index.html.twig');
    }

    #[Route('/por-autor', name: 'app_relatorio_por_autor')]
    public function porAutor(): Response
    {
        try {
            return $this->render('relatorio/por_autor.html.twig', [
                'agrupado' => $this->relatorioService->getLivrosPorAutor(),
            ]);
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_relatorio_index');
        }
    }

    #[Route('/por-editora', name: 'app_relatorio_por_editora')]
    public function porEditora(): Response
    {
        try {
            return $this->render('relatorio/por_editora.html.twig', [
                'agrupado' => $this->relatorioService->getLivrosPorEditora(),
            ]);
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_relatorio_index');
        }
    }

    #[Route('/por-assunto', name: 'app_relatorio_por_assunto')]
    public function porAssunto(): Response
    {
        try {
            return $this->render('relatorio/por_assunto.html.twig', [
                'agrupado' => $this->relatorioService->getLivrosPorAssunto(),
            ]);
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_relatorio_index');
        }
    }

    #[Route('/por-autor/pdf', name: 'app_relatorio_por_autor_pdf')]
    public function porAutorPdf(): Response
    {
        try {
            return $this->pdfService->generate('relatorio/pdf_por_autor.html.twig', [
                'agrupado' => $this->relatorioService->getLivrosPorAutor(),
            ], 'relatorio-por-autor.pdf');
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_relatorio_index');
        }
    }

    #[Route('/por-editora/pdf', name: 'app_relatorio_por_editora_pdf')]
    public function porEditoraPdf(): Response
    {
        try {
            return $this->pdfService->generate('relatorio/pdf_por_editora.html.twig', [
                'agrupado' => $this->relatorioService->getLivrosPorEditora(),
            ], 'relatorio-por-editora.pdf');
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_relatorio_index');
        }
    }

    #[Route('/por-assunto/pdf', name: 'app_relatorio_por_assunto_pdf')]
    public function porAssuntoPdf(): Response
    {
        try {
            return $this->pdfService->generate('relatorio/pdf_por_assunto.html.twig', [
                'agrupado' => $this->relatorioService->getLivrosPorAssunto(),
            ], 'relatorio-por-assunto.pdf');
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_relatorio_index');
        }
    }

    #[Route('/ranking', name: 'app_relatorio_ranking')]
    public function ranking(): Response
    {
        try {
            return $this->render('relatorio/ranking.html.twig', $this->relatorioService->getRankingData());
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_relatorio_index');
        }
    }
}
