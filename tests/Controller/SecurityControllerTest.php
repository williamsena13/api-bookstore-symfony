<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageCarrega(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
    }

    public function testLoginComCredenciaisInvalidas(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Entrar', [
            '_username' => 'invalido@test.com',
            '_password' => 'senhaerrada',
        ]);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.p-alert-danger');
    }

    public function testRedirecionaParaDashboardSeJaLogado(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdminUser($client));
        $client->request('GET', '/login');
        $this->assertResponseRedirects('/admin/');
    }

    public function testLogoutFunciona(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdminUser($client));
        $client->request('GET', '/logout');
        $this->assertResponseRedirects();
    }

    private function getAdminUser($client): \App\Entity\User
    {
        return static::getContainer()
            ->get('doctrine')
            ->getRepository(\App\Entity\User::class)
            ->findOneBy(['email' => 'admin@admin.com.br']);
    }
}
