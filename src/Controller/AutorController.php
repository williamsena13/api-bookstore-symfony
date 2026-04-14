<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Form\AutorType;
use App\Repository\AutorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/autores')]
class AutorController extends AbstractController
{
    #[Route('/', name: 'app_autor_index', methods: ['GET'])]
    public function index(AutorRepository $repository): Response
    {
        return $this->render('autor/index.html.twig', [
            'autores' => $repository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_autor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $autor = new Autor();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($autor);
            $em->flush();

            return new JsonResponse(['success' => true, 'message' => 'Autor criado com sucesso!']);
        }

        return $this->render('autor/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_autor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Autor $autor, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $autor->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'Autor atualizado com sucesso!']);
        }

        return $this->render('autor/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_autor_delete', methods: ['POST'])]
    public function delete(Request $request, Autor $autor, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $autor->getId(), $request->request->get('_token'))) {
            $em->remove($autor);
            $em->flush();
            $this->addFlash('success', 'Autor excluído com sucesso!');
        }

        return $this->redirectToRoute('app_autor_index');
    }
}
