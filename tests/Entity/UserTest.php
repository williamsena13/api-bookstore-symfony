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

    public function testIdInicialmenteNulo(): void
    {
        $this->assertNull($this->user->getId());
    }

    public function testSetGetNome(): void
    {
        $this->user->setNome('William Sena');
        $this->assertSame('William Sena', $this->user->getNome());
    }

    public function testSetGetEmail(): void
    {
        $this->user->setEmail('admin@admin.com.br');
        $this->assertSame('admin@admin.com.br', $this->user->getEmail());
    }

    public function testGetUserIdentifierRetornaEmail(): void
    {
        $this->user->setEmail('admin@admin.com.br');
        $this->assertSame('admin@admin.com.br', $this->user->getUserIdentifier());
    }

    public function testSetGetPassword(): void
    {
        $this->user->setPassword('hashed_password');
        $this->assertSame('hashed_password', $this->user->getPassword());
    }

    public function testRolesSempreContemRoleUser(): void
    {
        $roles = $this->user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testSetRolesAdicionaRoleAdmin(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $roles = $this->user->getRoles();
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
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
