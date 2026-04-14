<?php

namespace App\Controller;

use App\Repository\AssuntoRepository;
use App\Repository\AutorRepository;
use App\Repository\EditoraRepository;
use App\Repository\LivroRepository;
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
            'totalLivros' => $livroRepository->count([]),
            'totalAutores' => $autorRepository->count([]),
            'totalEditoras' => $editoraRepository->count([]),
            'totalAssuntos' => $assuntoRepository->count([]),
        ]);
    }

    #[Route('/tecnologias', name: 'app_home_tech')]
    public function tech(): Response
    {
        return $this->render('home/tech.html.twig');
    }
}
