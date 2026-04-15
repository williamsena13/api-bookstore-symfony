<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testGetUserIdentifierRetornaEmail(): void
    {
        $this->user->setEmail('admin@admin.com.br');
        $this->assertSame('admin@admin.com.br', $this->user->getUserIdentifier());
    }

    public function testRolesSempreContemRoleUser(): void
    {
        $this->assertContains('ROLE_USER', $this->user->getRoles());
    }

    public function testSetRolesAdicionaRoleAdmin(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
        $this->assertContains('ROLE_USER', $this->user->getRoles());
    }

    public function testRolesSemDuplicatas(): void
    {
        $this->user->setRoles(['ROLE_USER', 'ROLE_USER']);
        $roles = $this->user->getRoles();
        $this->assertSame(array_unique($roles), $roles);
    }

    public function testEraseCredentialsNaoLancaExcecao(): void
    {
        $this->expectNotToPerformAssertions();
        $this->user->eraseCredentials();
    }
}
