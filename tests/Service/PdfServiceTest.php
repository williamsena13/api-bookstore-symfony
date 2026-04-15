<?php

namespace App\Tests\Service;

use App\Service\PdfService;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class PdfServiceTest extends TestCase
{
    public function testGenerateRetornaPdfComHeadersCorretos(): void
    {
        $dados = ['agrupado' => ['Autor A' => [['titulo' => 'Livro 1']]]];

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('relatorio/pdf.html.twig', $dados)
            ->willReturn('<html><body>Relatório</body></html>');

        $response = (new PdfService($twig))->generate('relatorio/pdf.html.twig', $dados, 'relatorio.pdf');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('relatorio.pdf', $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }
}
