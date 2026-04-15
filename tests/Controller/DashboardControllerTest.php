<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testDashboardRedirecionaSemAutenticacao(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/');
        $this->assertResponseRedirects('/login');
    }

    public function testDashboardCarregaAutenticado(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $client->request('GET', '/admin/');
        $this->assertResponseIsSuccessful();
    }

    public function testDashboardContemContadores(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdmin($client));
        $crawler = $client->request('GET', '/admin/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.stat-card');
    }

    private function getAdmin($client): User
    {
        return static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@admin.com.br']);
    }
}
