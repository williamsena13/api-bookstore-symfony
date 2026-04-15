<?php

namespace App\Service;

use App\Entity\Livraria;
use App\Repository\LivrariaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LivrariaService
{
    private string $uploadDir;

    public function __construct(
        private LivrariaRepository $repository,
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        string $projectDir,
    ) {
        $this->uploadDir = $projectDir . '/public/uploads/livraria';
    }

    public function getCurrent(): Livraria
    {
        return $this->repository->findCurrent() ?? new Livraria();
    }

    public function save(Livraria $livraria, ?UploadedFile $faviconFile, ?UploadedFile $logoNavbarFile): void
    {
        try {
            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0755, true);
            }

            if ($faviconFile) {
                $this->removeOldFile($livraria->getFavicon());
                $filename = 'favicon_' . uniqid() . '.' . $faviconFile->guessExtension();
                $faviconFile->move($this->uploadDir, $filename);
                $livraria->setFavicon('/uploads/livraria/' . $filename);
            }

            if ($logoNavbarFile) {
                $this->removeOldFile($livraria->getLogoNavbar());
                $filename = 'navbar_' . uniqid() . '.' . $logoNavbarFile->guessExtension();
                $logoNavbarFile->move($this->uploadDir, $filename);
                $livraria->setLogoNavbar('/uploads/livraria/' . $filename);
            }

            $livraria->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($livraria);
            $this->em->flush();
        } catch (\Throwable $e) {
            $this->logger->error('Erro ao salvar livraria: ' . $e->getMessage());
            throw new \RuntimeException('Erro ao salvar os dados da livraria.');
        }
    }

    private function removeOldFile(?string $path): void
    {
        if (!$path) return;
        $fullPath = dirname($this->uploadDir) . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
