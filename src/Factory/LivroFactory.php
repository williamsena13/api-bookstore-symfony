<?php

namespace App\Factory;

use App\Entity\Livro;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Livro>
 */
final class LivroFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Livro::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'titulo'        => self::faker()->sentence(3),
            'isbn'          => self::faker()->isbn13(),
            'anoPublicacao' => self::faker()->numberBetween(1950, 2024),
            'edicao'        => self::faker()->numberBetween(1, 10),
            'preco'         => self::faker()->randomFloat(2, 20, 300),
            'editora'       => EditoraFactory::new(),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Livro $livro): void {
            if ($livro->getAutores()->isEmpty()) {
                $livro->addAutor(AutorFactory::createOne()->_real());
            }
            if ($livro->getAssuntos()->isEmpty()) {
                $livro->addAssunto(AssuntoFactory::createOne()->_real());
            }
        });
    }
}
