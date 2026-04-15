<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RelatorioControllerTest extends WebTestCase
{
    public function testRelatorioIndexRedirecionaSemAutenticacao(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/relatorios/');
        $this->assertResponseRedirects('/login');
    }

    public function testRelatorioIndexCarregaAutenticado(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/relatorios/');
        $this->assertResponseIsSuccessful();
    }

    public function testRelatorioPorAutorCarrega(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/relatorios/por-autor');
        $this->assertResponseIsSuccessful();
    }

    public function testRelatorioPorEditoraCarrega(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/relatorios/por-editora');
        $this->assertResponseIsSuccessful();
    }

    public function testRelatorioPorAssuntoCarrega(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/relatorios/por-assunto');
        $this->assertResponseIsSuccessful();
    }

    public function testRelatorioRankingCarrega(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/relatorios/ranking');
        $this->assertResponseIsSuccessful();
    }

    public function testRelatorioPorAutorPdfRetornaPdf(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/relatorios/por-autor/pdf');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/pdf');
    }

    public function testRelatorioPorEditoraPdfRetornaPdf(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/relatorios/por-editora/pdf');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/pdf');
    }

    public function testRelatorioPorAssuntoPdfRetornaPdf(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/relatorios/por-assunto/pdf');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/pdf');
    }

    private function getAdmin($client): User
    {
        return static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@admin.com.br']);
    }
}
