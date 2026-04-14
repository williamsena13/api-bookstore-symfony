<?php

namespace App\Controller;

use App\Entity\Livro;
use App\Form\LivroType;
use App\Repository\LivroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/livros')]
class LivroController extends AbstractController
{
    #[Route('/', name: 'app_livro_index', methods: ['GET'])]
    public function index(LivroRepository $repository): Response
    {
        return $this->render('livro/index.html.twig', [
            'livros' => $repository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_livro_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $livro = new Livro();
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($livro);
            $em->flush();

            return new JsonResponse(['success' => true, 'message' => 'Livro criado com sucesso!']);
        }

        return $this->render('livro/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_livro_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livro $livro, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livro->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'Livro atualizado com sucesso!']);
        }

        return $this->render('livro/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_livro_delete', methods: ['POST'])]
    public function delete(Request $request, Livro $livro, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livro->getId(), $request->request->get('_token'))) {
            $em->remove($livro);
            $em->flush();
            $this->addFlash('success', 'Livro excluído com sucesso!');
        }

        return $this->redirectToRoute('app_livro_index');
    }
}
