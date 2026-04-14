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
            'totalLivros' => $livroRepository->count([]),
            'totalAutores' => $autorRepository->count([]),
            'totalEditoras' => $editoraRepository->count([]),
            'totalAssuntos' => $assuntoRepository->count([]),
            'totalUsuarios' => $userRepository->count([]),
        ]);
    }
}
