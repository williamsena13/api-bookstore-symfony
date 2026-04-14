<?php

namespace App\Entity;

use App\Repository\EditoraRepository;
use App\Trait\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EditoraRepository::class)]
#[UniqueEntity(fields: ['nome'], message: 'Já existe uma editora com este nome.')]
class Editora
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'O nome é obrigatório.')]
    #[Assert\Length(max: 255, maxMessage: 'O nome deve ter no máximo {{ limit }} caracteres.')]
    private ?string $nome = null;

    public function __construct()
    {
        $this->initTimestamps();
    }

    public function getId(): ?int { return $this->id; }

    public function getNome(): ?string { return $this->nome; }
    public function setNome(string $nome): static { $this->nome = $nome; return $this; }

    public function __toString(): string { return $this->nome ?? ''; }
}
