<?php

namespace App\Controller;

use App\Form\LivrariaType;
use App\Service\LivrariaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/livraria')]
class LivrariaController extends AbstractController
{
    public function __construct(private LivrariaService $livrariaService) {}

    #[Route('/', name: 'app_livraria_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $livraria = $this->livrariaService->getCurrent();
        $form = $this->createForm(LivrariaType::class, $livraria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->livrariaService->save($livraria);
                $this->addFlash('success', 'Dados da livraria salvos com sucesso!');
            } catch (\RuntimeException $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('app_livraria_edit');
        }

        return $this->render('livraria/edit.html.twig', [
            'form' => $form->createView(),
            'livraria' => $livraria,
        ]);
    }
}
