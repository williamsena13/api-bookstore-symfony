# 📚 Bookstore — Sistema de Gerenciamento de Livros

Sistema MVP para gerenciamento de livros desenvolvido com Symfony 6.4, incluindo CRUD completo, relatórios com VIEWs SQL e PDF, dashboard com gráficos, sistema de temas customizável e painel administrativo protegido por autenticação.

## 🔗 Repositório

[https://github.com/williamsena13/api-bookstore-symfony](https://github.com/williamsena13/api-bookstore-symfony)

## ✍️ Autor

**William B. Sena**

## 🛠️ Tecnologias

### Backend
- PHP >= 8.1
- Symfony 6.4 LTS
- Doctrine ORM 3.6
- DomPDF 3.x (geração de relatórios em PDF)
- Symfony Security (autenticação, CSRF, roles)
- Symfony Mime (upload de arquivos)

### Frontend
- Twig 3.x (template engine)
- PrimeFlex + PrimeIcons (layout e ícones)
- Bootstrap 5.3 (modais, tabs, grid)
- DataTables 2.x (grids com filtro, ordenação e paginação)
- TomSelect (multi-select com filtro e checkboxes)
- Chart.js 4.x (gráficos no dashboard e relatórios)
- Leaflet.js (mapa interativo)
- JavaScript Vanilla (AJAX, máscaras, ViaCEP)

### Banco de Dados
- MySQL 8.0
- VIEWs SQL para relatórios

## 📦 Funcionalidades

### Cadastros (CRUD)
- **Livros** — título, ISBN, edição, ano, preço, editora (ManyToOne), autores e assuntos (ManyToMany)
- **Autores** — nome com validação de unicidade
- **Editoras** — nome com validação de unicidade
- **Assuntos** — descrição com validação de unicidade
- **Usuários** — nome, e-mail, senha (hash), perfil (Admin/Usuário), foto de perfil

### Formulários via Modal AJAX
- Criação e edição sem recarregar a página
- TomSelect nos multi-selects com filtro e checkboxes
- Máscara de moeda (R$) e telefone
- Modal de confirmação de exclusão
- Tratamento de erros específicos (UniqueConstraint, ForeignKey)

### Dashboard
- 6 cards de contadores (Livros, Autores, Editoras, Assuntos, Usuários, Relatórios)
- 5 gráficos Chart.js (autores, editoras, assuntos, preços, visão geral)
- Dados da livraria com contato e endereço

### Relatórios
- **Por Autor** — dashboard com gráficos + tabela agrupada + PDF + CSV
- **Por Editora** — dashboard com gráficos + tabela agrupada + PDF + CSV
- **Por Assunto** — dashboard com gráficos + tabela agrupada + PDF + CSV
- **Rankings** — top autores, editoras, assuntos e livros mais caros
- Todos baseados em VIEWs SQL no banco de dados

### Configuração da Livraria
- **Dados gerais** — nome, descrição, telefone, e-mail
- **Endereço** — CEP com preenchimento automático via API ViaCEP
- **Mapa** — Leaflet.js com busca por endereço (Nominatim), geolocalização e marcador arrastável
- **Identidade visual** — upload de favicon e logo do navbar
- **Tema** — 8 presets de cores + personalização individual com preview ao vivo

### Sistema de Temas
- Cores persistidas no banco de dados (cor primária, secundária, sidebar)
- Aplicação via CSS Variables injetadas pelo `_theme_css.html.twig`
- Dark mode por usuário (botão lua/sol no topbar, salvo no localStorage)
- Preview ao vivo na página de configuração

### Autenticação e Segurança
- Login com e-mail e senha
- Controle de acesso por `ROLE_ADMIN`
- Proteção CSRF em todos os formulários
- Hash de senha automático
- Proteção contra auto-exclusão de usuário
- Tratamento de erros específicos (UniqueConstraint, ForeignKey, Throwable)

### Perfil do Usuário
- Edição de nome e e-mail
- Upload de foto de perfil com preview
- Alteração de senha (opcional)
- Dropdown no topbar com avatar e menu

### Landing Page
- Página pública com funcionalidades do sistema
- Contadores em tempo real do banco
- Seção de mapa (quando coordenadas configuradas)
- Página de tecnologias utilizadas

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
| `livraria` | Configurações da livraria (singleton) |

### VIEWs SQL
| VIEW | Descrição |
|---|---|
| `vw_relatorio_livros_por_autor` | Livros agrupados por autor |
| `vw_relatorio_livros_por_editora` | Livros agrupados por editora |
| `vw_relatorio_livros_por_assunto` | Livros agrupados por assunto |

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

# 7. Criar pasta de uploads
mkdir -p public/uploads/livraria public/uploads/usuarios

# 8. Limpar cache
php bin/console cache:clear

# 9. Iniciar servidor de desenvolvimento
symfony server:start
# ou
php -S localhost:8000 -t public/
```

### Acesso

- **URL**: http://localhost:8000
- **Landing page**: http://localhost:8000/
- **Login**: http://localhost:8000/login
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
│   ├── HomeController.php
│   ├── LivrariaController.php
│   ├── LivroController.php
│   ├── RelatorioController.php
│   ├── SecurityController.php
│   └── UserController.php
├── Entity/
│   ├── Assunto.php
│   ├── Autor.php
│   ├── Editora.php
│   ├── Livraria.php
│   ├── Livro.php
│   └── User.php
├── Form/
│   ├── AssuntoType.php
│   ├── AutorType.php
│   ├── EditoraType.php
│   ├── LivrariaType.php
│   ├── LivroType.php
│   ├── ProfileType.php
│   └── UserType.php
├── Repository/
│   ├── AssuntoRepository.php
│   ├── AutorRepository.php
│   ├── EditoraRepository.php
│   ├── LivrariaRepository.php
│   ├── LivroRepository.php
│   └── UserRepository.php
├── Service/
│   ├── LivrariaService.php
│   ├── PdfService.php
│   └── RelatorioService.php
├── Trait/
│   └── TimestampTrait.php
└── Twig/
    └── AppExtension.php

public/
├── css/
│   ├── app.css
│   └── landing.css
└── js/
    ├── admin-theme.js
    ├── datatable.js
    ├── masks.js
    ├── modal-crud.js
    ├── tomselect-init.js
    └── viacep.js
```

## 🏗️ Arquitetura

- **MVC** — Controller → Service → Repository → Entity
- **DRY** — `TimestampTrait` para campos `createdAt`/`updatedAt`
- **SRP** — Services isolam lógica de negócio, Controllers apenas orquestram
- **Twig Global** — `AppExtension` disponibiliza dados da livraria em todos os templates
- **CSS Variables** — `_theme_css.html.twig` injeta cores do banco como variáveis CSS
- **Error Handling** — Try/catch específicos para UniqueConstraint, ForeignKey e Throwable

## 📄 Licença

Proprietário — Todos os direitos reservados.
