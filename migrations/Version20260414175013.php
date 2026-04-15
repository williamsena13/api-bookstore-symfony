<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260414175013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Criar VIEWs de relatório: por autor, por editora e por assunto';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE OR REPLACE VIEW vw_relatorio_livros_por_autor AS
            SELECT a.id AS autor_id
                 , a.nome AS autor_nome
                 , l.id AS livro_id
                 , l.titulo AS livro_titulo
                 , e.nome AS editora
                 , l.edicao
                 , l.ano_publicacao
                 , l.preco
                 , GROUP_CONCAT(DISTINCT s.descricao ORDER BY s.descricao SEPARATOR ', ') AS assuntos
              FROM autor a
             INNER JOIN livro_autor la ON la.autor_id = a.id
             INNER JOIN livro l ON l.id = la.livro_id
             LEFT JOIN editora e ON e.id = l.editora_id
             LEFT JOIN livro_assunto ls ON ls.livro_id = l.id
             LEFT JOIN assunto s ON s.id = ls.assunto_id
             GROUP BY a.id, a.nome, l.id, l.titulo, e.nome, l.edicao, l.ano_publicacao, l.preco
             ORDER BY a.nome, l.titulo
        ");

        $this->addSql("CREATE OR REPLACE VIEW vw_relatorio_livros_por_editora AS
            SELECT e.id AS editora_id
                 , e.nome AS editora_nome
                 , l.id AS livro_id
                 , l.titulo AS livro_titulo
                 , l.edicao
                 , l.ano_publicacao
                 , l.preco
                 , GROUP_CONCAT(DISTINCT a.nome ORDER BY a.nome SEPARATOR ', ') AS autores
                 , GROUP_CONCAT(DISTINCT s.descricao ORDER BY s.descricao SEPARATOR ', ') AS assuntos
              FROM editora e
             INNER JOIN livro l ON l.editora_id = e.id
              LEFT JOIN livro_autor la ON la.livro_id = l.id
              LEFT JOIN autor a ON a.id = la.autor_id
              LEFT JOIN livro_assunto ls ON ls.livro_id = l.id
              LEFT JOIN assunto s ON s.id = ls.assunto_id
          GROUP BY e.id, e.nome, l.id, l.titulo, l.edicao, l.ano_publicacao, l.preco
          ORDER BY e.nome, l.titulo
        ");

        $this->addSql("CREATE OR REPLACE VIEW vw_relatorio_livros_por_assunto AS
            SELECT s.id AS assunto_id
                 , s.descricao AS assunto
                 , l.id AS livro_id
                 , l.titulo AS livro_titulo
                 , e.nome AS editora
                 , l.edicao
                 , l.ano_publicacao
                 , l.preco
                 , GROUP_CONCAT(DISTINCT a.nome ORDER BY a.nome SEPARATOR ', ') AS autores
              FROM assunto s
             INNER JOIN livro_assunto ls ON ls.assunto_id = s.id
             INNER JOIN livro l ON l.id = ls.livro_id
             LEFT JOIN editora e ON e.id = l.editora_id
             LEFT JOIN livro_autor la ON la.livro_id = l.id
             LEFT JOIN autor a ON a.id = la.autor_id
            GROUP BY s.id, s.descricao, l.id, l.titulo, e.nome, l.edicao, l.ano_publicacao, l.preco
            ORDER BY s.descricao, l.titulo
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS vw_relatorio_livros_por_autor');
        $this->addSql('DROP VIEW IF EXISTS vw_relatorio_livros_por_editora');
        $this->addSql('DROP VIEW IF EXISTS vw_relatorio_livros_por_assunto');
    }
}
