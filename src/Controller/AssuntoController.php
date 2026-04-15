<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Form\AssuntoType;
use App\Repository\AssuntoRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/assuntos')]
class AssuntoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {}

    #[Route('/', name: 'app_assunto_index', methods: ['GET'])]
    public function index(Request $request, AssuntoRepository $repository): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        return $this->render('assunto/index.html.twig', [
            'paginacao' => $repository->findPaginated($page),
        ]);
    }

    #[Route('/novo', name: 'app_assunto_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $assunto = new Assunto();
        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->em->persist($assunto);
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Assunto criado com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe um assunto com esta descrição.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao criar assunto: ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao salvar o assunto. Tente novamente.'], 500);
            }
        }

        return $this->render('assunto/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_assunto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Assunto $assunto, AssuntoRepository $repository): Response
    {
        if (!$assunto) {
            $this->addFlash('danger', 'Assunto não encontrado.');
            return $this->redirectToRoute('app_assunto_index');
        }

        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $assunto->setUpdatedAt(new \DateTimeImmutable());
                $this->em->flush();
                $this->addFlash('success', 'Assunto atualizado com sucesso!');
                return $this->redirectToRoute('app_assunto_edit', ['id' => $assunto->getId()]);
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('danger', 'Já existe um assunto com esta descrição.');
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao atualizar assunto #' . $assunto->getId() . ': ' . $e->getMessage());
                $this->addFlash('danger', 'Erro interno ao atualizar o assunto.');
            }
        }

        return $this->render('assunto/edit.html.twig', [
            'form'    => $form->createView(),
            'assunto' => $assunto,
            'livros'  => $repository->findLivrosByAssunto($assunto->getId()),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_assunto_delete', methods: ['POST'])]
    public function delete(Request $request, ?Assunto $assunto, AssuntoRepository $repository): Response
    {
        if (!$assunto) {
            $this->addFlash('danger', 'Assunto não encontrado.');
            return $this->redirectToRoute('app_assunto_index');
        }

        if (!$this->isCsrfTokenValid('delete' . $assunto->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de segurança inválido.');
            return $this->redirectToRoute('app_assunto_index');
        }

        $livros = $repository->findLivrosByAssunto($assunto->getId());
        if (!empty($livros)) {
            $titulos = implode(', ', array_map(fn($l) => '“' . $l['titulo'] . '”', array_slice($livros, 0, 5)));
            $extra = count($livros) > 5 ? ' e mais ' . (count($livros) - 5) . ' livro(s)' : '';
            $this->addFlash('danger', 'Não é possível excluir “' . $assunto->getDescricao() . '” pois está vinculado a: ' . $titulos . $extra . '.');
            return $this->redirectToRoute('app_assunto_index');
        }

        try {
            $this->em->remove($assunto);
            $this->em->flush();
            $this->addFlash('success', 'Assunto excluído com sucesso!');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao excluir assunto #' . $assunto->getId() . ': ' . $e->getMessage());
            $this->addFlash('danger', 'Erro interno ao excluir o assunto. Tente novamente.');
        }

        return $this->redirectToRoute('app_assunto_index');
    }
}
