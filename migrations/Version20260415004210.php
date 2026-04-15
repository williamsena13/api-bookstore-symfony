<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260415004210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livraria ADD latitude NUMERIC(10, 7) DEFAULT NULL, ADD longitude NUMERIC(10, 7) DEFAULT NULL, ADD favicon VARCHAR(255) DEFAULT NULL, ADD logo_navbar VARCHAR(255) DEFAULT NULL, ADD cor_primaria VARCHAR(7) DEFAULT NULL, ADD cor_secundaria VARCHAR(7) DEFAULT NULL, ADD cor_sidebar VARCHAR(7) DEFAULT NULL, ADD tema_admin VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livraria DROP latitude, DROP longitude, DROP favicon, DROP logo_navbar, DROP cor_primaria, DROP cor_secundaria, DROP cor_sidebar, DROP tema_admin');
    }
}
