<?php

namespace App\Controller;

use App\Repository\AssuntoRepository;
use App\Repository\EditoraRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(AssuntoRepository $assuntoRepository, EditoraRepository $editoraRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'totalAssuntos' => $assuntoRepository->count([]),
            'totalEditoras' => $editoraRepository->count([]),
        ]);
    }
}
