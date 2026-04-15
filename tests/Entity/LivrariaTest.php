<?php

namespace App\Tests\Entity;

use App\Entity\Livraria;
use PHPUnit\Framework\TestCase;

class LivrariaTest extends TestCase
{
    private Livraria $livraria;

    protected function setUp(): void
    {
        $this->livraria = new Livraria();
    }

    public function testValoresPadraoNoConstructor(): void
    {
        $this->assertSame('#6366f1', $this->livraria->getCorPrimaria());
        $this->assertSame('#22c55e', $this->livraria->getCorSecundaria());
        $this->assertSame('#1e1e2e', $this->livraria->getCorSidebar());
        $this->assertSame('light', $this->livraria->getTemaAdmin());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->livraria->getCreatedAt());
    }

    public function testHasCoordinatesRequerLatitudeELongitude(): void
    {
        $this->assertFalse($this->livraria->hasCoordinates());

        $this->livraria->setLatitude('-23.5505');
        $this->assertFalse($this->livraria->hasCoordinates(), 'Só latitude não basta');

        $this->livraria->setLongitude('-46.6333');
        $this->assertTrue($this->livraria->hasCoordinates());
    }

    public function testEnderecoCompleto(): void
    {
        $this->assertSame('', $this->livraria->getEnderecoCompleto());

        $this->livraria->setLogradouro('Av. Paulista')->setNumero('1000')
            ->setCidade('São Paulo')->setUf('SP')->setCep('01310-100');

        $endereco = $this->livraria->getEnderecoCompleto();
        $this->assertStringContainsString('Av. Paulista', $endereco);
        $this->assertStringContainsString('São Paulo/SP', $endereco);
        $this->assertStringContainsString('CEP: 01310-100', $endereco);
    }
}
