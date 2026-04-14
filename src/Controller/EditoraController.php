<?php

namespace App\Controller;

use App\Entity\Editora;
use App\Form\EditoraType;
use App\Repository\EditoraRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/editoras')]
class EditoraController extends AbstractController
{
    #[Route('/', name: 'app_editora_index', methods: ['GET'])]
    public function index(EditoraRepository $repository): Response
    {
        return $this->render('editora/index.html.twig', [
            'editoras' => $repository->findAll(),
        ]);
    }

    #[Route('/nova', name: 'app_editora_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $editora = new Editora();
        $form = $this->createForm(EditoraType::class, $editora);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($editora);
            $em->flush();

            return new JsonResponse(['success' => true, 'message' => 'Editora criada com sucesso!']);
        }

        return $this->render('editora/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_editora_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Editora $editora, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EditoraType::class, $editora);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $editora->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'Editora atualizada com sucesso!']);
        }

        return $this->render('editora/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_editora_delete', methods: ['POST'])]
    public function delete(Request $request, Editora $editora, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $editora->getId(), $request->request->get('_token'))) {
            $em->remove($editora);
            $em->flush();
            $this->addFlash('success', 'Editora excluída com sucesso!');
        }

        return $this->redirectToRoute('app_editora_index');
    }
}
