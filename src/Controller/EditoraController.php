<?php

namespace App\Controller;

use App\Entity\Editora;
use App\Form\EditoraType;
use App\Repository\EditoraRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/editoras')]
class EditoraController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {}

    #[Route('/', name: 'app_editora_index', methods: ['GET'])]
    public function index(Request $request, EditoraRepository $repository): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        return $this->render('editora/index.html.twig', [
            'paginacao' => $repository->findPaginated($page),
        ]);
    }

    #[Route('/nova', name: 'app_editora_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $editora = new Editora();
        $form = $this->createForm(EditoraType::class, $editora);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->em->persist($editora);
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Editora criada com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe uma editora com este nome.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao criar editora: ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao salvar a editora. Tente novamente.'], 500);
            }
        }

        return $this->render('editora/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_editora_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Editora $editora, EditoraRepository $repository): Response
    {
        if (!$editora) {
            $this->addFlash('danger', 'Editora não encontrada.');
            return $this->redirectToRoute('app_editora_index');
        }

        $form = $this->createForm(EditoraType::class, $editora);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $editora->setUpdatedAt(new \DateTimeImmutable());
                $this->em->flush();
                $this->addFlash('success', 'Editora atualizada com sucesso!');
                return $this->redirectToRoute('app_editora_edit', ['id' => $editora->getId()]);
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('danger', 'Já existe uma editora com este nome.');
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao atualizar editora #' . $editora->getId() . ': ' . $e->getMessage());
                $this->addFlash('danger', 'Erro interno ao atualizar a editora.');
            }
        }

        return $this->render('editora/edit.html.twig', [
            'form'    => $form->createView(),
            'editora' => $editora,
            'livros'  => $repository->findLivrosByEditora($editora->getId()),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_editora_delete', methods: ['POST'])]
    public function delete(Request $request, ?Editora $editora, EditoraRepository $repository): Response
    {
        if (!$editora) {
            $this->addFlash('danger', 'Editora não encontrada.');
            return $this->redirectToRoute('app_editora_index');
        }

        if (!$this->isCsrfTokenValid('delete' . $editora->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de segurança inválido.');
            return $this->redirectToRoute('app_editora_index');
        }

        $livros = $repository->findLivrosByEditora($editora->getId());
        if (!empty($livros)) {
            $titulos = implode(', ', array_map(fn($l) => '“' . $l['titulo'] . '”', array_slice($livros, 0, 5)));
            $extra = count($livros) > 5 ? ' e mais ' . (count($livros) - 5) . ' livro(s)' : '';
            $this->addFlash('danger', 'Não é possível excluir “' . $editora->getNome() . '” pois está vinculada a: ' . $titulos . $extra . '.');
            return $this->redirectToRoute('app_editora_index');
        }

        try {
            $this->em->remove($editora);
            $this->em->flush();
            $this->addFlash('success', 'Editora excluída com sucesso!');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao excluir editora #' . $editora->getId() . ': ' . $e->getMessage());
            $this->addFlash('danger', 'Erro interno ao excluir a editora. Tente novamente.');
        }

        return $this->redirectToRoute('app_editora_index');
    }
}
