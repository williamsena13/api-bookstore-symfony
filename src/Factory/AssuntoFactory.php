<?php

namespace App\Factory;

use App\Entity\Assunto;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Assunto>
 */
final class AssuntoFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Assunto::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'descricao' => self::faker()->unique()->words(3, true),
        ];
    }
}
