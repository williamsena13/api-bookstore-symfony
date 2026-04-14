<?php

namespace App\Twig;

use App\Service\LivrariaService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private LivrariaService $livrariaService) {}

    public function getGlobals(): array
    {
        return [
            'livraria' => $this->livrariaService->getCurrent(),
        ];
    }
}
