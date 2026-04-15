<?php

namespace App\Controller;

use App\Repository\AssuntoRepository;
use App\Repository\AutorRepository;
use App\Repository\EditoraRepository;
use App\Repository\LivroRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        LivroRepository $livroRepository,
        AutorRepository $autorRepository,
        EditoraRepository $editoraRepository,
        AssuntoRepository $assuntoRepository,
        UserRepository $userRepository,
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'totalLivros'    => $livroRepository->count([]),
            'totalAutores'   => $autorRepository->count([]),
            'totalEditoras'  => $editoraRepository->count([]),
            'totalAssuntos'  => $assuntoRepository->count([]),
            'totalUsuarios'  => $userRepository->count([]),

            // Gráfico: top 8 autores por livros
            'topAutores'     => $autorRepository->findTopAuthorsByBooks(8),

            // Gráfico: top 6 editoras por livros
            'topEditoras'    => $editoraRepository->findTopPublishersByBooks(6),

            // Gráfico: top 8 assuntos por livros
            'topAssuntos'    => $assuntoRepository->findTopSubjectsByBooks(8),

            // Gráfico: top 8 livros mais caros
            'livrosMaisCaros' => $livroRepository->findMostExpensive(8),

            // Gráfico: distribuição de livros por editora (todos)
            'livrosPorEditora' => $editoraRepository->findTopPublishersByBooks(10),
        ]);
    }
}
