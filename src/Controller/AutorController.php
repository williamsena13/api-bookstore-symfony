<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Form\AutorType;
use App\Repository\AutorRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/autores')]
class AutorController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {}

    #[Route('/', name: 'app_autor_index', methods: ['GET'])]
    public function index(AutorRepository $repository): Response
    {
        return $this->render('autor/index.html.twig', [
            'autores' => $repository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_autor_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $autor = new Autor();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->em->persist($autor);
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Autor criado com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe um autor com este nome.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao criar autor: ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao salvar o autor. Tente novamente.'], 500);
            }
        }

        return $this->render('autor/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_autor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Autor $autor): Response
    {
        if (!$autor) {
            return new JsonResponse(['success' => false, 'message' => 'Autor não encontrado.'], 404);
        }

        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $autor->setUpdatedAt(new \DateTimeImmutable());
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Autor atualizado com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe um autor com este nome.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao atualizar autor #' . $autor->getId() . ': ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao atualizar o autor. Tente novamente.'], 500);
            }
        }

        return $this->render('autor/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_autor_delete', methods: ['POST'])]
    public function delete(Request $request, ?Autor $autor): Response
    {
        if (!$autor) {
            $this->addFlash('danger', 'Autor não encontrado.');
            return $this->redirectToRoute('app_autor_index');
        }

        if (!$this->isCsrfTokenValid('delete' . $autor->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de segurança inválido.');
            return $this->redirectToRoute('app_autor_index');
        }

        try {
            $this->em->remove($autor);
            $this->em->flush();
            $this->addFlash('success', 'Autor excluído com sucesso!');
        } catch (ForeignKeyConstraintViolationException) {
            $this->addFlash('danger', 'Não é possível excluir este autor pois está vinculado a um ou mais livros.');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao excluir autor #' . $autor->getId() . ': ' . $e->getMessage());
            $this->addFlash('danger', 'Erro interno ao excluir o autor. Tente novamente.');
        }

        return $this->redirectToRoute('app_autor_index');
    }
}
