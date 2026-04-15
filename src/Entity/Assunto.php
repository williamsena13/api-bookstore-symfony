<?php

namespace App\Entity;

use App\Repository\AssuntoRepository;
use App\Trait\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssuntoRepository::class)]
#[UniqueEntity(fields: ['descricao'], message: 'Já existe um assunto com esta descrição.')]
class Assunto
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'A descrição é obrigatória.')]
    #[Assert\Length(max: 255, maxMessage: 'A descrição deve ter no máximo {{ limit }} caracteres.')]
    private ?string $descricao = null;

    public function __construct()
    {
        $this->initTimestamps();
    }

    public function getId(): ?int { return $this->id; }

    public function getDescricao(): ?string { return $this->descricao; }
    public function setDescricao(string $descricao): static { $this->descricao = $descricao; return $this; }

    public function __toString(): string { return $this->descricao ?? ''; }
}
