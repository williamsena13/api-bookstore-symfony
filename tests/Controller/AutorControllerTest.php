<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AutorControllerTest extends WebTestCase
{
    public function testIndexRedirecionaSemAutenticacao(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/autores/');
        $this->assertResponseRedirects('/login');
    }

    public function testIndexCarregaAutenticado(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/autores/');
        $this->assertResponseIsSuccessful();
    }

    public function testNovoFormularioCarrega(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/autores/novo');
        $this->assertResponseIsSuccessful();
    }

    public function testEditarAutorInexistenteRedireciona(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/autores/999999/editar');
        $this->assertResponseRedirects('/admin/autores/');
    }

    public function testExcluirSemCsrfRedirecionaComErro(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('POST', '/admin/autores/1/excluir', ['_token' => 'invalido']);
        $this->assertResponseRedirects('/admin/autores/');
        $client->followRedirect();
        $this->assertSelectorExists('.p-alert-danger');
    }

    public function testExcluirAutorInexistenteRedireciona(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('POST', '/admin/autores/999999/excluir', ['_token' => 'qualquer']);
        $this->assertResponseRedirects('/admin/autores/');
    }

    private function getAdmin($client): User
    {
        return static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@admin.com.br']);
    }
}
