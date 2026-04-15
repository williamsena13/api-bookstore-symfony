<?php

namespace App\Tests\Entity;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Editora;
use App\Entity\Livro;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidacaoEntidadesTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    private function campos(object $entity): array
    {
        return array_map(
            fn($e) => $e->getPropertyPath(),
            iterator_to_array($this->validator->validate($entity))
        );
    }

    // ===== AUTOR =====

    public function testAutorNomeObrigatorio(): void
    {
        $this->assertContains('nome', $this->campos(new Autor()));
    }

    public function testAutorNomeValido(): void
    {
        $autor = (new Autor())->setNome('Machado de Assis');
        $this->assertNotContains('nome', $this->campos($autor));
    }

    public function testAutorNomeMaximo255Caracteres(): void
    {
        $autor = (new Autor())->setNome(str_repeat('a', 256));
        $this->assertContains('nome', $this->campos($autor));
    }

    // ===== EDITORA =====

    public function testEditoraNomeObrigatorio(): void
    {
        $this->assertContains('nome', $this->campos(new Editora()));
    }

    public function testEditoraNomeValido(): void
    {
        $editora = (new Editora())->setNome('Companhia das Letras');
        $this->assertNotContains('nome', $this->campos($editora));
    }

    public function testEditoraNomeMaximo255Caracteres(): void
    {
        $editora = (new Editora())->setNome(str_repeat('e', 256));
        $this->assertContains('nome', $this->campos($editora));
    }

    // ===== ASSUNTO =====

    public function testAssuntoDescricaoObrigatoria(): void
    {
        $this->assertContains('descricao', $this->campos(new Assunto()));
    }

    public function testAssuntoDescricaoValida(): void
    {
        $assunto = (new Assunto())->setDescricao('Romance');
        $this->assertNotContains('descricao', $this->campos($assunto));
    }

    public function testAssuntoDescricaoMaximo255Caracteres(): void
    {
        $assunto = (new Assunto())->setDescricao(str_repeat('a', 256));
        $this->assertContains('descricao', $this->campos($assunto));
    }

    // ===== LIVRO =====

    public function testLivroTituloObrigatorio(): void
    {
        $this->assertContains('titulo', $this->campos(new Livro()));
    }

    public function testLivroSemAutoresInvalido(): void
    {
        $livro = (new Livro())->setTitulo('Dom Casmurro');
        $this->assertContains('autores', $this->campos($livro));
    }

    public function testLivroSemAssuntosInvalido(): void
    {
        $livro = (new Livro())->setTitulo('Dom Casmurro');
        $this->assertContains('assuntos', $this->campos($livro));
    }

    public function testLivroPrecoNegativoInvalido(): void
    {
        $livro = (new Livro())->setTitulo('Dom Casmurro')->setPreco('-10.00');
        $this->assertContains('preco', $this->campos($livro));
    }

    public function testLivroAnoNegativoInvalido(): void
    {
        $livro = (new Livro())->setTitulo('Dom Casmurro')->setAnoPublicacao(-1);
        $this->assertContains('anoPublicacao', $this->campos($livro));
    }

    public function testLivroEdicaoNegativaInvalida(): void
    {
        $livro = (new Livro())->setTitulo('Dom Casmurro')->setEdicao(-1);
        $this->assertContains('edicao', $this->campos($livro));
    }

    public function testLivroPrecoZeroValido(): void
    {
        $livro = (new Livro())->setTitulo('Dom Casmurro')->setPreco('0.00');
        $this->assertNotContains('preco', $this->campos($livro));
    }

    public function testLivroTituloMaximo255Caracteres(): void
    {
        $livro = (new Livro())->setTitulo(str_repeat('t', 256));
        $this->assertContains('titulo', $this->campos($livro));
    }
}
