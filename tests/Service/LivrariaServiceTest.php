<?php

namespace App\Tests\Service;

use App\Entity\Livraria;
use App\Repository\LivrariaRepository;
use App\Service\LivrariaService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LivrariaServiceTest extends TestCase
{
    private LivrariaService $service;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $repository   = $this->createMock(LivrariaRepository::class);
        $this->em     = $this->createMock(EntityManagerInterface::class);

        $repository->method('findCurrent')->willReturn(null);

        $this->service = new LivrariaService(
            $repository,
            $this->em,
            $this->createMock(LoggerInterface::class),
            sys_get_temp_dir(),
        );
    }

    public function testSavePersisteLivrariaEAtualizaUpdatedAt(): void
    {
        $livraria = (new Livraria())->setNome('Livraria Teste');

        $this->em->expects($this->once())->method('persist')->with($livraria);
        $this->em->expects($this->once())->method('flush');

        $antes = new \DateTimeImmutable();
        $this->service->save($livraria, null, null);

        $this->assertGreaterThanOrEqual($antes, $livraria->getUpdatedAt());
    }

    public function testSaveLancaExcecaoEmErroDeFlush(): void
    {
        $this->em->method('flush')->willThrowException(new \RuntimeException('DB error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erro ao salvar os dados da livraria.');

        $this->service->save(new Livraria(), null, null);
    }
}
