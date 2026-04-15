<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260414163136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livro (id INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(255) NOT NULL, isbn VARCHAR(20) DEFAULT NULL, ano_publicacao INT DEFAULT NULL, edicao INT DEFAULT NULL, preco NUMERIC(10, 2) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, editora_id INT DEFAULT NULL, INDEX IDX_4CB6A6876262BA26 (editora_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE livro_autor (livro_id INT NOT NULL, autor_id INT NOT NULL, INDEX IDX_67499925864C5AF (livro_id), INDEX IDX_674999214D45BBE (autor_id), PRIMARY KEY(livro_id, autor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE livro_assunto (livro_id INT NOT NULL, assunto_id INT NOT NULL, INDEX IDX_53C2C52A5864C5AF (livro_id), INDEX IDX_53C2C52A4CE74285 (assunto_id), PRIMARY KEY(livro_id, assunto_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE livro ADD CONSTRAINT FK_4CB6A6876262BA26 FOREIGN KEY (editora_id) REFERENCES editora (id)');
        $this->addSql('ALTER TABLE livro_autor ADD CONSTRAINT FK_67499925864C5AF FOREIGN KEY (livro_id) REFERENCES livro (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE livro_autor ADD CONSTRAINT FK_674999214D45BBE FOREIGN KEY (autor_id) REFERENCES autor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE livro_assunto ADD CONSTRAINT FK_53C2C52A5864C5AF FOREIGN KEY (livro_id) REFERENCES livro (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE livro_assunto ADD CONSTRAINT FK_53C2C52A4CE74285 FOREIGN KEY (assunto_id) REFERENCES assunto (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livro DROP FOREIGN KEY FK_4CB6A6876262BA26');
        $this->addSql('ALTER TABLE livro_autor DROP FOREIGN KEY FK_67499925864C5AF');
        $this->addSql('ALTER TABLE livro_autor DROP FOREIGN KEY FK_674999214D45BBE');
        $this->addSql('ALTER TABLE livro_assunto DROP FOREIGN KEY FK_53C2C52A5864C5AF');
        $this->addSql('ALTER TABLE livro_assunto DROP FOREIGN KEY FK_53C2C52A4CE74285');
        $this->addSql('DROP TABLE livro');
        $this->addSql('DROP TABLE livro_autor');
        $this->addSql('DROP TABLE livro_assunto');
    }
}
