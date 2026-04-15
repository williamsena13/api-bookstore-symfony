<?php

namespace App\Controller;

use App\Repository\AssuntoRepository;
use App\Repository\AutorRepository;
use App\Repository\EditoraRepository;
use App\Repository\LivroRepository;
use App\Service\RelatorioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        LivroRepository $livroRepository,
        AutorRepository $autorRepository,
        EditoraRepository $editoraRepository,
        AssuntoRepository $assuntoRepository,
    ): Response {
        return $this->render('home/index.html.twig', [
            'totalLivros'   => $livroRepository->count([]),
            'totalAutores'  => $autorRepository->count([]),
            'totalEditoras' => $editoraRepository->count([]),
            'totalAssuntos' => $assuntoRepository->count([]),
        ]);
    }

    #[Route('/tecnologias', name: 'app_home_tech')]
    public function tech(): Response
    {
        return $this->render('home/tech.html.twig');
    }

    #[Route('/livros', name: 'app_public_livros')]
    public function livros(LivroRepository $repo): Response
    {
        return $this->render('public/livros.html.twig', [
            'livros' => $repo->findAllWithRelations(),
        ]);
    }

    #[Route('/autores', name: 'app_public_autores')]
    public function autores(AutorRepository $repo): Response
    {
        return $this->render('public/autores.html.twig', [
            'autores' => $repo->findAllWithCount(),
        ]);
    }

    #[Route('/editoras', name: 'app_public_editoras')]
    public function editoras(EditoraRepository $repo): Response
    {
        return $this->render('public/editoras.html.twig', [
            'editoras' => $repo->findAllWithCount(),
        ]);
    }

    #[Route('/assuntos', name: 'app_public_assuntos')]
    public function assuntos(AssuntoRepository $repo): Response
    {
        return $this->render('public/assuntos.html.twig', [
            'assuntos' => $repo->findAllWithCount(),
        ]);
    }

    #[Route('/relatorios', name: 'app_public_relatorios')]
    public function relatorios(): Response
    {
        return $this->render('public/relatorios.html.twig');
    }

    #[Route('/relatorios/por-autor', name: 'app_public_relatorio_por_autor')]
    public function relatoriosPorAutor(RelatorioService $service): Response
    {
        return $this->render('public/relatorio_por_autor.html.twig', [
            'agrupado' => $service->getLivrosPorAutor(),
        ]);
    }

    #[Route('/relatorios/por-editora', name: 'app_public_relatorio_por_editora')]
    public function relatoriosPorEditora(RelatorioService $service): Response
    {
        return $this->render('public/relatorio_por_editora.html.twig', [
            'agrupado' => $service->getLivrosPorEditora(),
        ]);
    }

    #[Route('/relatorios/por-assunto', name: 'app_public_relatorio_por_assunto')]
    public function relatoriosPorAssunto(RelatorioService $service): Response
    {
        return $this->render('public/relatorio_por_assunto.html.twig', [
            'agrupado' => $service->getLivrosPorAssunto(),
        ]);
    }

    #[Route('/relatorios/ranking', name: 'app_public_relatorio_ranking')]
    public function relatoriosRanking(RelatorioService $service): Response
    {
        return $this->render('public/relatorio_ranking.html.twig', $service->getRankingData());
    }
}
