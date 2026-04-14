<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Form\AssuntoType;
use App\Repository\AssuntoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/assuntos')]
class AssuntoController extends AbstractController
{
    #[Route('/', name: 'app_assunto_index', methods: ['GET'])]
    public function index(AssuntoRepository $repository): Response
    {
        return $this->render('assunto/index.html.twig', [
            'assuntos' => $repository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_assunto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $assunto = new Assunto();
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($assunto);
            $em->flush();

            return new JsonResponse(['success' => true, 'message' => 'Assunto criado com sucesso!']);
        }

        return $this->render('assunto/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_assunto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Assunto $assunto, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assunto->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'Assunto atualizado com sucesso!']);
        }

        return $this->render('assunto/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_assunto_delete', methods: ['POST'])]
    public function delete(Request $request, Assunto $assunto, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $assunto->getId(), $request->request->get('_token'))) {
            $em->remove($assunto);
            $em->flush();
            $this->addFlash('success', 'Assunto excluído com sucesso!');
        }

        return $this->redirectToRoute('app_assunto_index');
    }
}
