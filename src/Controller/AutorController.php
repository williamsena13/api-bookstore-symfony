<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Form\AutorType;
use App\Repository\AutorRepository;
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
    public function index(Request $request, AutorRepository $repository): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        return $this->render('autor/index.html.twig', [
            'paginacao' => $repository->findPaginated($page),
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
    public function edit(Request $request, ?Autor $autor, AutorRepository $repository): Response
    {
        if (!$autor) {
            $this->addFlash('danger', 'Autor não encontrado.');
            return $this->redirectToRoute('app_autor_index');
        }

        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $autor->setUpdatedAt(new \DateTimeImmutable());
                $this->em->flush();
                $this->addFlash('success', 'Autor atualizado com sucesso!');
                return $this->redirectToRoute('app_autor_edit', ['id' => $autor->getId()]);
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('danger', 'Já existe um autor com este nome.');
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao atualizar autor #' . $autor->getId() . ': ' . $e->getMessage());
                $this->addFlash('danger', 'Erro interno ao atualizar o autor.');
            }
        }

        return $this->render('autor/edit.html.twig', [
            'form'   => $form->createView(),
            'autor'  => $autor,
            'livros' => $repository->findLivrosByAutor($autor->getId()),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_autor_delete', methods: ['POST'])]
    public function delete(Request $request, ?Autor $autor, AutorRepository $repository): Response
    {
        if (!$autor) {
            $this->addFlash('danger', 'Autor não encontrado.');
            return $this->redirectToRoute('app_autor_index');
        }

        if (!$this->isCsrfTokenValid('delete' . $autor->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de segurança inválido.');
            return $this->redirectToRoute('app_autor_index');
        }

        $livros = $repository->findLivrosByAutor($autor->getId());
        if (!empty($livros)) {
            $titulos = implode(', ', array_map(fn($l) => '“' . $l['titulo'] . '”', array_slice($livros, 0, 5)));
            $extra = count($livros) > 5 ? ' e mais ' . (count($livros) - 5) . ' livro(s)' : '';
            $this->addFlash('danger', 'Não é possível excluir “' . $autor->getNome() . '” pois está vinculado a: ' . $titulos . $extra . '.');
            return $this->redirectToRoute('app_autor_index');
        }

        try {
            $this->em->remove($autor);
            $this->em->flush();
            $this->addFlash('success', 'Autor excluído com sucesso!');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao excluir autor #' . $autor->getId() . ': ' . $e->getMessage());
            $this->addFlash('danger', 'Erro interno ao excluir o autor. Tente novamente.');
        }

        return $this->redirectToRoute('app_autor_index');
    }
}
