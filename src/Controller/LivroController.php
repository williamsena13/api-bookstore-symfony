<?php

namespace App\Controller;

use App\Entity\Livro;
use App\Form\LivroType;
use App\Repository\LivroRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/livros')]
class LivroController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {}

    #[Route('/', name: 'app_livro_index', methods: ['GET'])]
    public function index(Request $request, LivroRepository $repository): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $paginacao = $repository->findPaginated($page);

        $editoras = array_values(array_unique(array_filter(array_column(
            $repository->findAllWithRelations(), 'editora'
        ))));
        sort($editoras);

        return $this->render('livro/index.html.twig', [
            'paginacao' => $paginacao,
            'editoras'  => $editoras,
        ]);
    }

    #[Route('/novo', name: 'app_livro_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $livro = new Livro();
        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->em->persist($livro);
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Livro criado com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe um livro com estes dados.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao criar livro: ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao salvar o livro. Tente novamente.'], 500);
            }
        }

        return $this->render('livro/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_livro_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Livro $livro): Response
    {
        if (!$livro) {
            return new JsonResponse(['success' => false, 'message' => 'Livro não encontrado.'], 404);
        }

        $form = $this->createForm(LivroType::class, $livro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $livro->setUpdatedAt(new \DateTimeImmutable());
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Livro atualizado com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe um livro com estes dados.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao atualizar livro #' . $livro->getId() . ': ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao atualizar o livro. Tente novamente.'], 500);
            }
        }

        return $this->render('livro/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_livro_delete', methods: ['POST'])]
    public function delete(Request $request, ?Livro $livro): Response
    {
        if (!$livro) {
            $this->addFlash('danger', 'Livro não encontrado.');
            return $this->redirectToRoute('app_livro_index');
        }

        if (!$this->isCsrfTokenValid('delete' . $livro->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de segurança inválido.');
            return $this->redirectToRoute('app_livro_index');
        }

        try {
            $this->em->remove($livro);
            $this->em->flush();
            $this->addFlash('success', 'Livro excluído com sucesso!');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao excluir livro #' . $livro->getId() . ': ' . $e->getMessage());
            $this->addFlash('danger', 'Erro interno ao excluir o livro. Tente novamente.');
        }

        return $this->redirectToRoute('app_livro_index');
    }
}
