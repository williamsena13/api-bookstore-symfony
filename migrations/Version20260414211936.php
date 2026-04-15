<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260414211936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livraria (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(100) NOT NULL, descricao VARCHAR(255) DEFAULT NULL, telefone VARCHAR(20) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, cep VARCHAR(9) DEFAULT NULL, logradouro VARCHAR(255) DEFAULT NULL, numero VARCHAR(20) DEFAULT NULL, complemento VARCHAR(100) DEFAULT NULL, bairro VARCHAR(100) DEFAULT NULL, cidade VARCHAR(100) DEFAULT NULL, uf VARCHAR(2) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE livraria');
    }
}
