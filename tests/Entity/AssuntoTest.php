<?php

namespace App\Tests\Entity;

use App\Entity\Assunto;
use PHPUnit\Framework\TestCase;

class AssuntoTest extends TestCase
{
    private Assunto $assunto;

    protected function setUp(): void
    {
        $this->assunto = new Assunto();
    }

    public function testIdInicialmenteNulo(): void
    {
        $this->assertNull($this->assunto->getId());
    }

    public function testSetGetDescricao(): void
    {
        $this->assunto->setDescricao('Ficção Científica');
        $this->assertSame('Ficção Científica', $this->assunto->getDescricao());
    }

    public function testToStringRetornaDescricao(): void
    {
        $this->assunto->setDescricao('Romance');
        $this->assertSame('Romance', (string) $this->assunto);
    }

    public function testToStringSemDescricaoRetornaStringVazia(): void
    {
        $this->assertSame('', (string) $this->assunto);
    }

    public function testCreatedAtInicializadoNoConstructor(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->assunto->getCreatedAt());
    }

    public function testUpdatedAtInicialmenteNulo(): void
    {
        $this->assertNull($this->assunto->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $dt = new \DateTimeImmutable('2026-01-01');
        $this->assunto->setUpdatedAt($dt);
        $this->assertSame($dt, $this->assunto->getUpdatedAt());
    }
}
