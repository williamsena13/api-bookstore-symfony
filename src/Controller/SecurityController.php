<?php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/admin/perfil', name: 'app_profile', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        LoggerInterface $logger,
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Upload de foto
                $fotoFile = $form->get('fotoFile')->getData();
                if ($fotoFile) {
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/usuarios';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    // Remove foto antiga
                    if ($user->getFoto()) {
                        $oldFile = $this->getParameter('kernel.project_dir') . '/public' . $user->getFoto();
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    $filename = 'user_' . $user->getId() . '_' . uniqid() . '.' . $fotoFile->guessExtension();
                    $fotoFile->move($uploadDir, $filename);
                    $user->setFoto('/uploads/usuarios/' . $filename);
                }

                // Atualizar senha se preenchida
                $plain = $form->get('plainPassword')->getData();
                if ($plain) {
                    $user->setPassword($hasher->hashPassword($user, $plain));
                }

                $em->flush();
                $this->addFlash('success', 'Perfil atualizado com sucesso!');
            } catch (\Throwable $e) {
                $logger->error('Erro ao atualizar perfil: ' . $e->getMessage());
                $this->addFlash('danger', 'Erro ao salvar o perfil. Tente novamente.');
            }

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('security/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
