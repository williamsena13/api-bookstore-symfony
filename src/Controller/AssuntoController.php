<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Form\AssuntoType;
use App\Repository\AssuntoRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
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
    public function index(AssuntoRepository $repository): Response
    {
        return $this->render('assunto/index.html.twig', [
            'assuntos' => $repository->findAll(),
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
    public function edit(Request $request, ?Assunto $assunto): Response
    {
        if (!$assunto) {
            return new JsonResponse(['success' => false, 'message' => 'Assunto não encontrado.'], 404);
        }

        $form = $this->createForm(AssuntoType::class, $assunto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $assunto->setUpdatedAt(new \DateTimeImmutable());
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Assunto atualizado com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe um assunto com esta descrição.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao atualizar assunto #' . $assunto->getId() . ': ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao atualizar o assunto. Tente novamente.'], 500);
            }
        }

        return $this->render('assunto/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_assunto_delete', methods: ['POST'])]
    public function delete(Request $request, ?Assunto $assunto): Response
    {
        if (!$assunto) {
            $this->addFlash('danger', 'Assunto não encontrado.');
            return $this->redirectToRoute('app_assunto_index');
        }

        if (!$this->isCsrfTokenValid('delete' . $assunto->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de segurança inválido.');
            return $this->redirectToRoute('app_assunto_index');
        }

        try {
            $this->em->remove($assunto);
            $this->em->flush();
            $this->addFlash('success', 'Assunto excluído com sucesso!');
        } catch (ForeignKeyConstraintViolationException) {
            $this->addFlash('danger', 'Não é possível excluir este assunto pois está vinculado a um ou mais livros.');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao excluir assunto #' . $assunto->getId() . ': ' . $e->getMessage());
            $this->addFlash('danger', 'Erro interno ao excluir o assunto. Tente novamente.');
        }

        return $this->redirectToRoute('app_assunto_index');
    }
}
