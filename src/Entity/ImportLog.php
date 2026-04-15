<?php

namespace App\Entity;

use App\Repository\ImportLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportLogRepository::class)]
#[ORM\Table(name: 'import_log')]
class ImportLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $tipo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $busca = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $limite = 0;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $idioma = null;

    #[ORM\Column(type: 'boolean')]
    private bool $dryRun = false;

    #[ORM\Column(type: Types::INTEGER)]
    private int $importados = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $ignorados = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $erros = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $sucesso = false;

    #[ORM\Column(type: Types::INTEGER)]
    private int $exitCode = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $output = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $usuario = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $duracaoSegundos = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTipo(): ?string { return $this->tipo; }
    public function setTipo(string $tipo): static { $this->tipo = $tipo; return $this; }
    public function getBusca(): ?string { return $this->busca; }
    public function setBusca(?string $busca): static { $this->busca = $busca; return $this; }
    public function getLimite(): int { return $this->limite; }
    public function setLimite(int $limite): static { $this->limite = $limite; return $this; }
    public function getIdioma(): ?string { return $this->idioma; }
    public function setIdioma(?string $idioma): static { $this->idioma = $idioma; return $this; }
    public function isDryRun(): bool { return $this->dryRun; }
    public function setDryRun(bool $dryRun): static { $this->dryRun = $dryRun; return $this; }
    public function getImportados(): int { return $this->importados; }
    public function setImportados(int $importados): static { $this->importados = $importados; return $this; }
    public function getIgnorados(): int { return $this->ignorados; }
    public function setIgnorados(int $ignorados): static { $this->ignorados = $ignorados; return $this; }
    public function getErros(): int { return $this->erros; }
    public function setErros(int $erros): static { $this->erros = $erros; return $this; }
    public function isSucesso(): bool { return $this->sucesso; }
    public function setSucesso(bool $sucesso): static { $this->sucesso = $sucesso; return $this; }
    public function getExitCode(): int { return $this->exitCode; }
    public function setExitCode(int $exitCode): static { $this->exitCode = $exitCode; return $this; }
    public function getOutput(): ?string { return $this->output; }
    public function setOutput(?string $output): static { $this->output = $output; return $this; }
    public function getUsuario(): ?string { return $this->usuario; }
    public function setUsuario(?string $usuario): static { $this->usuario = $usuario; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function getDuracaoSegundos(): ?string { return $this->duracaoSegundos; }
    public function setDuracaoSegundos(?string $duracaoSegundos): static { $this->duracaoSegundos = $duracaoSegundos; return $this; }
    public function getTotal(): int { return $this->importados + $this->ignorados + $this->erros; }
    public function getTaxaSucesso(): float { return $this->getTotal() > 0 ? round(($this->importados / $this->getTotal()) * 100, 1) : 0; }
}
