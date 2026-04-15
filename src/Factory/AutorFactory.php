<?php

namespace App\Factory;

use App\Entity\Autor;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Autor>
 */
final class AutorFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Autor::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'nome' => self::faker()->name(),
        ];
    }
}
