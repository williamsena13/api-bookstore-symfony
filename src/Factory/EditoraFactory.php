<?php

namespace App\Factory;

use App\Entity\Editora;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Editora>
 */
final class EditoraFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Editora::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'nome' => self::faker()->company(),
        ];
    }
}
