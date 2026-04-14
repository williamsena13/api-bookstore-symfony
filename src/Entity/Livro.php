<?php

namespace App\Entity;

use App\Repository\LivroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivroRepository::class)]
class Livro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'O título é obrigatório.')]
    #[Assert\Length(max: 255, maxMessage: 'O título deve ter no máximo {{ limit }} caracteres.')]
    private ?string $titulo = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $isbn = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Positive(message: 'O ano deve ser um número positivo.')]
    private ?int $anoPublicacao = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'O preço deve ser zero ou positivo.')]
    private ?string $preco = null;

    #[ORM\ManyToOne(targetEntity: Editora::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Editora $editora = null;

    #[ORM\ManyToMany(targetEntity: Autor::class)]
    #[ORM\JoinTable(name: 'livro_autor')]
    #[Assert\Count(min: 1, minMessage: 'Selecione pelo menos um autor.')]
    private Collection $autores;

    #[ORM\ManyToMany(targetEntity: Assunto::class)]
    #[ORM\JoinTable(name: 'livro_assunto')]
    #[Assert\Count(min: 1, minMessage: 'Selecione pelo menos um assunto.')]
    private Collection $assuntos;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->autores = new ArrayCollection();
        $this->assuntos = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitulo(): ?string { return $this->titulo; }
    public function setTitulo(string $titulo): static { $this->titulo = $titulo; return $this; }

    public function getIsbn(): ?string { return $this->isbn; }
    public function setIsbn(?string $isbn): static { $this->isbn = $isbn; return $this; }

    public function getAnoPublicacao(): ?int { return $this->anoPublicacao; }
    public function setAnoPublicacao(?int $anoPublicacao): static { $this->anoPublicacao = $anoPublicacao; return $this; }

    public function getPreco(): ?string { return $this->preco; }
    public function setPreco(?string $preco): static { $this->preco = $preco; return $this; }

    public function getEditora(): ?Editora { return $this->editora; }
    public function setEditora(?Editora $editora): static { $this->editora = $editora; return $this; }

    public function getAutores(): Collection { return $this->autores; }
    public function addAutor(Autor $autor): static { if (!$this->autores->contains($autor)) { $this->autores->add($autor); } return $this; }
    public function removeAutor(Autor $autor): static { $this->autores->removeElement($autor); return $this; }

    public function getAssuntos(): Collection { return $this->assuntos; }
    public function addAssunto(Assunto $assunto): static { if (!$this->assuntos->contains($assunto)) { $this->assuntos->add($assunto); } return $this; }
    public function removeAssunto(Assunto $assunto): static { $this->assuntos->removeElement($assunto); return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function __toString(): string { return $this->titulo ?? ''; }
}
