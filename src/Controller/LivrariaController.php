<?php

namespace App\Controller;

use App\Form\LivrariaType;
use App\Service\LivrariaService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/livraria')]
class LivrariaController extends AbstractController
{
    public function __construct(
        private LivrariaService $livrariaService,
        private LoggerInterface $logger,
    ) {}

    #[Route('/', name: 'app_livraria_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $livraria = $this->livrariaService->getCurrent();
        $form = $this->createForm(LivrariaType::class, $livraria);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->logger->info('FORM SUBMITTED - isValid: ' . ($form->isValid() ? 'YES' : 'NO'));
            $this->logger->info('POST corPrimaria: ' . $request->request->all('livraria')['corPrimaria'] ?? 'NULL');
            $this->logger->info('POST corSidebar: ' . $request->request->all('livraria')['corSidebar'] ?? 'NULL');

            foreach ($form->getErrors(true) as $error) {
                $this->logger->error('FORM ERROR [' . $error->getOrigin()?->getName() . ']: ' . $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->logger->info('SAVING - corPrimaria: ' . $livraria->getCorPrimaria());
                $this->livrariaService->save(
                    $livraria,
                    $form->get('faviconFile')->getData(),
                    $form->get('logoNavbarFile')->getData(),
                );
                $this->addFlash('success', 'Salvo! Cor: ' . $livraria->getCorPrimaria());
            } catch (\RuntimeException $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('app_livraria_edit');
        }

        return $this->render('livraria/edit.html.twig', [
            'form' => $form->createView(),
            'livraria' => $livraria,
        ]);
    }
}
