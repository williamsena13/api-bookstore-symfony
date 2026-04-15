<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LivroControllerTest extends WebTestCase
{
    public function testIndexRedirecionaSemAutenticacao(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/livros/');
        $this->assertResponseRedirects('/login');
    }

    public function testIndexCarregaAutenticado(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/livros/');
        $this->assertResponseIsSuccessful();
    }

    public function testIndexPaginacaoPage1(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/livros/?page=1');
        $this->assertResponseIsSuccessful();
    }

    public function testIndexPaginacaoPageInvalidaUsaPage1(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/livros/?page=-5');
        $this->assertResponseIsSuccessful();
    }

    public function testNovoFormularioCarrega(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/livros/novo');
        $this->assertResponseIsSuccessful();
    }

    public function testEditarLivroInexistenteRetorna404(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/livros/999999/editar');
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testExcluirSemCsrfRedirecionaComErro(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('POST', '/admin/livros/1/excluir', ['_token' => 'token_invalido']);
        $this->assertResponseRedirects('/admin/livros/');
        $client->followRedirect();
        $this->assertSelectorExists('.p-alert-danger');
    }

    private function getAdmin($client): User
    {
        return static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@admin.com.br']);
    }
}
