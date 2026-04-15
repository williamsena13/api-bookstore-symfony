<?php

namespace App\Entity;

use App\Repository\LivrariaRepository;
use App\Trait\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivrariaRepository::class)]
class Livraria
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ===== INFORMAÇÕES GERAIS =====
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'O nome é obrigatório.')]
    private ?string $nome = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descricao = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telefone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $email = null;

    // ===== ENDEREÇO =====
    #[ORM\Column(length: 9, nullable: true)]
    private ?string $cep = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logradouro = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $complemento = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $bairro = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $cidade = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $uf = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    // ===== IDENTIDADE VISUAL =====
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $favicon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoNavbar = null;

    // ===== TEMA =====
    #[ORM\Column(length: 7, nullable: true)]
    private ?string $corPrimaria = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $corSecundaria = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $corSidebar = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $temaAdmin = null; // 'light' | 'dark'

    public function __construct()
    {
        $this->initTimestamps();
        $this->corPrimaria = '#6366f1';
        $this->corSecundaria = '#22c55e';
        $this->corSidebar = '#1e1e2e';
        $this->temaAdmin = 'light';
    }

    public function getId(): ?int { return $this->id; }

    public function getNome(): ?string { return $this->nome; }
    public function setNome(string $nome): static { $this->nome = $nome; return $this; }

    public function getDescricao(): ?string { return $this->descricao; }
    public function setDescricao(?string $descricao): static { $this->descricao = $descricao; return $this; }

    public function getTelefone(): ?string { return $this->telefone; }
    public function setTelefone(?string $telefone): static { $this->telefone = $telefone; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): static { $this->email = $email; return $this; }

    public function getCep(): ?string { return $this->cep; }
    public function setCep(?string $cep): static { $this->cep = $cep; return $this; }

    public function getLogradouro(): ?string { return $this->logradouro; }
    public function setLogradouro(?string $logradouro): static { $this->logradouro = $logradouro; return $this; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(?string $numero): static { $this->numero = $numero; return $this; }

    public function getComplemento(): ?string { return $this->complemento; }
    public function setComplemento(?string $complemento): static { $this->complemento = $complemento; return $this; }

    public function getBairro(): ?string { return $this->bairro; }
    public function setBairro(?string $bairro): static { $this->bairro = $bairro; return $this; }

    public function getCidade(): ?string { return $this->cidade; }
    public function setCidade(?string $cidade): static { $this->cidade = $cidade; return $this; }

    public function getUf(): ?string { return $this->uf; }
    public function setUf(?string $uf): static { $this->uf = $uf; return $this; }

    public function getLatitude(): ?string { return $this->latitude; }
    public function setLatitude(?string $latitude): static { $this->latitude = $latitude; return $this; }

    public function getLongitude(): ?string { return $this->longitude; }
    public function setLongitude(?string $longitude): static { $this->longitude = $longitude; return $this; }

    public function getFavicon(): ?string { return $this->favicon; }
    public function setFavicon(?string $favicon): static { $this->favicon = $favicon; return $this; }

    public function getLogoNavbar(): ?string { return $this->logoNavbar; }
    public function setLogoNavbar(?string $logoNavbar): static { $this->logoNavbar = $logoNavbar; return $this; }

    public function getCorPrimaria(): ?string { return $this->corPrimaria; }
    public function setCorPrimaria(?string $corPrimaria): static { $this->corPrimaria = $corPrimaria; return $this; }

    public function getCorSecundaria(): ?string { return $this->corSecundaria; }
    public function setCorSecundaria(?string $corSecundaria): static { $this->corSecundaria = $corSecundaria; return $this; }

    public function getCorSidebar(): ?string { return $this->corSidebar; }
    public function setCorSidebar(?string $corSidebar): static { $this->corSidebar = $corSidebar; return $this; }

    public function getTemaAdmin(): ?string { return $this->temaAdmin; }
    public function setTemaAdmin(?string $temaAdmin): static { $this->temaAdmin = $temaAdmin; return $this; }

    public function getEnderecoCompleto(): string
    {
        $partes = array_filter([
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->bairro,
            $this->cidade ? $this->cidade . ($this->uf ? '/' . $this->uf : '') : null,
            $this->cep ? 'CEP: ' . $this->cep : null,
        ]);
        return implode(', ', $partes);
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function __toString(): string { return $this->nome ?? ''; }
}
