<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260415023953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE import_log (id INT AUTO_INCREMENT NOT NULL, tipo VARCHAR(30) NOT NULL, busca VARCHAR(255) DEFAULT NULL, limite INT NOT NULL, idioma VARCHAR(10) DEFAULT NULL, dry_run TINYINT(1) NOT NULL, importados INT NOT NULL, ignorados INT NOT NULL, erros INT NOT NULL, sucesso TINYINT(1) NOT NULL, exit_code INT NOT NULL, output LONGTEXT DEFAULT NULL, usuario VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, duracao_segundos NUMERIC(8, 2) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE import_log');
    }
}
