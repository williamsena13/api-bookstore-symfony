# Bookstore — Sistema de Gerenciamento de Livros

Sistema MVP para gerenciamento de livros desenvolvido com Symfony 6.4, incluindo CRUD completo de Livros, Autores, Editoras e Assuntos, com painel administrativo protegido por autenticação.

## Repositório

[https://github.com/williamsena13/api-bookstore-symfony](https://github.com/williamsena13/api-bookstore-symfony)

## Autor

**William B. Sena**

## Tecnologias

- PHP >= 8.1
- Symfony 6.4
- Doctrine ORM 3.6
- Twig
- Bootstrap 5 + Bootstrap Icons
- MySQL 8.0

## Funcionalidades

- **Autenticação** — Login/logout com controle de acesso por `ROLE_ADMIN`
- **Dashboard** — Painel com contadores de cada módulo
- **CRUD Assuntos** — Cadastro, edição, exclusão e listagem
- **CRUD Autores** — Cadastro, edição, exclusão e listagem
- **CRUD Editoras** — Cadastro, edição, exclusão e listagem
- **CRUD Livros** — Cadastro com relacionamento ManyToMany (Autores, Assuntos) e ManyToOne (Editora)
- **Formulários via Modal** — Criação e edição via AJAX com feedback de sucesso

## 🗄️ Estrutura do Banco de Dados

| Tabela | Descrição |
|---|---|
| `user` | Usuários do sistema (autenticação) |
| `assunto` | Assuntos/categorias dos livros |
| `autor` | Autores |
| `editora` | Editoras |
| `livro` | Livros |
| `livro_autor` | Relacionamento Livro ↔ Autor (N:N) |
| `livro_assunto` | Relacionamento Livro ↔ Assunto (N:N) |

## ⚙️ Instalação

### Pré-requisitos

- PHP >= 8.1
- Composer
- MySQL 8.0+

### Passo a passo

```bash
# 1. Clonar o repositório
git clone https://github.com/williamsena13/api-bookstore-symfony.git
cd api-bookstore-symfony

# 2. Instalar dependências
composer install

# 3. Configurar variáveis de ambiente
cp .env .env.local
```

Edite o `.env.local` com a URL do seu banco de dados:

```env
DATABASE_URL="mysql://usuario:senha@127.0.0.1:3306/bookstore?serverVersion=8.0"
```

```bash
# 4. Criar banco de dados
php bin/console doctrine:database:create

# 5. Executar migrations
php bin/console doctrine:migrations:migrate --no-interaction

# 6. Criar usuário admin
php bin/console app:create-admin

# 7. Limpar cache
php bin/console cache:clear

# 8. Iniciar servidor de desenvolvimento
symfony server:start
# ou
php -S localhost:8000 -t public/
```

### Acesso

- **URL**: http://localhost:8000/login
- **E-mail**: `admin@admin.com.br`
- **Senha**: `admin`

## 📁 Estrutura do Projeto

```
src/
├── Command/
│   └── CreateAdminCommand.php
├── Controller/
│   ├── AssuntoController.php
│   ├── AutorController.php
│   ├── DashboardController.php
│   ├── EditoraController.php
│   ├── LivroController.php
│   └── SecurityController.php
├── Entity/
│   ├── Assunto.php
│   ├── Autor.php
│   ├── Editora.php
│   ├── Livro.php
│   └── User.php
├── Form/
│   ├── AssuntoType.php
│   ├── AutorType.php
│   ├── EditoraType.php
│   └── LivroType.php
└── Repository/
    ├── AssuntoRepository.php
    ├── AutorRepository.php
    ├── EditoraRepository.php
    ├── LivroRepository.php
    └── UserRepository.php
```

## 📄 Licença

Proprietário — Todos os direitos reservados.
