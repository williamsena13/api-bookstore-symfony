<?php

namespace App\DataFixtures;

use App\Factory\AssuntoFactory;
use App\Factory\AutorFactory;
use App\Factory\EditoraFactory;
use App\Factory\LivroFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Cria 10 autores, 5 editoras e 8 assuntos base
        AutorFactory::createMany(10);
        EditoraFactory::createMany(5);
        AssuntoFactory::createMany(8);

        // Cria 30 livros com relacionamentos aleatórios
        LivroFactory::createMany(30);
    }
}
