<?php

namespace App\Tests\Entity;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use PHPUnit\Framework\TestCase;

class LivroTest extends TestCase
{
    private Livro $livro;

    protected function setUp(): void
    {
        $this->livro = new Livro();
    }

    public function testToString(): void
    {
        $this->assertSame('', (string) $this->livro);
        $this->assertSame('Dom Casmurro', (string) $this->livro->setTitulo('Dom Casmurro'));
    }

    public function testTimestamps(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->livro->getCreatedAt());
        $this->assertNull($this->livro->getUpdatedAt());

        $dt = new \DateTimeImmutable();
        $this->livro->setUpdatedAt($dt);
        $this->assertSame($dt, $this->livro->getUpdatedAt());
    }

    public function testColecaoAutoresNaoDuplicaERemove(): void
    {
        $autor = (new Autor())->setNome('Machado de Assis');

        $this->livro->addAutor($autor);
        $this->livro->addAutor($autor);
        $this->assertCount(1, $this->livro->getAutores());

        $this->livro->removeAutor($autor);
        $this->assertCount(0, $this->livro->getAutores());
    }

    public function testColecaoAssuntosNaoDuplicaERemove(): void
    {
        $assunto = (new Assunto())->setDescricao('Romance');

        $this->livro->addAssunto($assunto);
        $this->livro->addAssunto($assunto);
        $this->assertCount(1, $this->livro->getAssuntos());

        $this->livro->removeAssunto($assunto);
        $this->assertCount(0, $this->livro->getAssuntos());
    }
}
