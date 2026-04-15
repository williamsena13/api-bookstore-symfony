<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuarios')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
        private LoggerInterface $logger,
    ) {}

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $repository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $repository->findAll(),
        ]);
    }

    #[Route('/novo', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setPassword($this->hasher->hashPassword($user, $form->get('plainPassword')->getData()));
                $this->em->persist($user);
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Usuário criado com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe um usuário com este e-mail.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao criar usuário: ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao salvar o usuário. Tente novamente.'], 500);
            }
        }

        return $this->render('user/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/editar', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ?User $user): Response
    {
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
        }

        $form = $this->createForm(UserType::class, $user, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($plain = $form->get('plainPassword')->getData()) {
                    $user->setPassword($this->hasher->hashPassword($user, $plain));
                }
                $this->em->flush();
                return new JsonResponse(['success' => true, 'message' => 'Usuário atualizado com sucesso!']);
            } catch (UniqueConstraintViolationException) {
                return new JsonResponse(['success' => false, 'message' => 'Já existe um usuário com este e-mail.'], 422);
            } catch (\Throwable $e) {
                $this->logger->error('Erro ao atualizar usuário #' . $user->getId() . ': ' . $e->getMessage());
                return new JsonResponse(['success' => false, 'message' => 'Erro interno ao atualizar o usuário. Tente novamente.'], 500);
            }
        }

        return $this->render('user/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, ?User $user): Response
    {
        if (!$user) {
            $this->addFlash('danger', 'Usuário não encontrado.');
            return $this->redirectToRoute('app_user_index');
        }

        if (!$this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de segurança inválido.');
            return $this->redirectToRoute('app_user_index');
        }

        if ($user === $this->getUser()) {
            $this->addFlash('danger', 'Você não pode excluir seu próprio usuário.');
            return $this->redirectToRoute('app_user_index');
        }

        try {
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash('success', 'Usuário excluído com sucesso!');
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao excluir usuário #' . $user->getId() . ': ' . $e->getMessage());
            $this->addFlash('danger', 'Erro interno ao excluir o usuário. Tente novamente.');
        }

        return $this->redirectToRoute('app_user_index');
    }
}
