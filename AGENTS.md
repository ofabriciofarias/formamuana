# FormaMuaná — Guia do Projeto

Sistema de gestão educacional para cursos e inscrições, com suporte a múltiplos perfis de usuário (Admin, Professor, Aluno), seleção de alunos via algoritmo genético e controle de frequência/notas.

## Ambiente de Desenvolvimento

**Pré-requisitos**: Docker e Docker Compose.

```bash
# Copiar variáveis de ambiente e ajustar credenciais
cp .env.example .env

# Subir os containers (na primeira vez, aguardar ~20s para o MySQL inicializar)
docker-compose up -d --build

# Instalar dependências do Composer (apenas se vendor/ não existir)
docker-compose exec app composer install
```

| Serviço     | URL                        |
|-------------|----------------------------|
| Aplicação   | http://localhost:8080      |
| phpMyAdmin  | http://localhost:8081      |
| MySQL       | localhost:3306             |

O banco `forma_muana` é criado automaticamente a partir de `banco_de_dados.sql` na primeira execução.

## Arquitetura

MVC com Front Controller. Toda requisição passa por `index.php`, que roteia via `?action=nome_acao`.

```
index.php           # Único ponto de entrada — carrega configs, instancia controllers, faz switch na action
config/             # Configurações de banco, e-mail e sessão (valores via getenv())
core/
  Database.php      # Singleton PDO para MySQL
  helpers.php       # Funções de role (is_admin, is_professor, is_aluno, is_logged_in) + BASE_URL
  GeneticAlgorithm.php  # Algoritmo genético para seleção de alunos
controllers/        # Lógica de negócio por domínio
models/             # Entidades do banco com métodos de persistência
views/              # Templates PHP puro, organizados por domínio
vendor/             # Dependências do Composer (não versionar)
uploads/            # Arquivos enviados pelos usuários (não versionar conteúdo)
banco_de_dados.sql  # Schema completo + dados iniciais
```

## Roteamento

Parâmetro GET `action` define o que executar. Exemplos:

```
?action=do_login          → UserController::do_login()
?action=dashboard         → DashboardController::index()
?action=professor_dashboard → ProfessorController::dashboard()
?action=aluno_dashboard   → AlunoController::dashboard()
?action=register-oferta   → OfertaController::create()
?action=lancar_frequencia → ProfessorController::lancarFrequencia()
```

Sem `action`, o roteamento padrão redireciona por role: Admin → `dashboard`, Professor → `professor_dashboard`, outros → home.

## Banco de Dados

**Nome**: `forma_muana` | **Engine**: InnoDB | **Charset**: utf8mb4

| Tabela               | Descrição                                  |
|----------------------|--------------------------------------------|
| `tipo_usuario`       | Lookup: 1=Admin, 2=Professor, 3=Aluno      |
| `usuarios`           | Usuários do sistema com uploads de documentos |
| `alunos`             | Dados biográficos e socioeconômicos (29 campos) |
| `oferta`             | Cursos/turmas oferecidas                   |
| `alunos_has_oferta`  | Inscrição + status de seleção + desempenho |
| `professor`          | Vínculo professor ↔ oferta                 |
| `aula`               | Plano de aulas por oferta                  |
| `lista_frequencia`   | Presença por aluno por aula                |

Conexão via `Database::getInstance()->getConnection()` (PDO, Singleton). Credenciais vêm das constantes `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` definidas em `config/database.php`.

## Sessão e Controle de Acesso

```php
$_SESSION['user_id']       // ID do usuário logado
$_SESSION['user_name']     // Nome do usuário
$_SESSION['user_role_id']  // 1=Admin | 2=Professor | 3=Aluno
```

Helpers disponíveis em qualquer ponto após carregar `core/helpers.php`:

```php
is_logged_in()   // verifica sessão ativa
is_admin()       // role_id == 1
is_professor()   // role_id == 2
is_aluno()       // role_id == 3
```

## Variáveis de Ambiente

Definidas no `.env` (não versionado). Ver `.env.example` como referência.

| Variável          | Descrição                       | Padrão          |
|-------------------|---------------------------------|-----------------|
| `DB_NAME`         | Nome do banco                   | forma_muana     |
| `DB_USER`         | Usuário do banco                | formamuana      |
| `DB_PASS`         | Senha do banco                  | secret          |
| `DB_ROOT_PASS`    | Senha do root MySQL             | rootsecret      |
| `MAIL_HOST`       | Servidor SMTP                   | smtp.gmail.com  |
| `MAIL_USERNAME`   | Email de envio                  | —               |
| `MAIL_PASSWORD`   | Senha de app do Gmail           | —               |
| `MAIL_SMTP_SECURE`| Criptografia (`ssl` ou `tls`)   | ssl             |
| `MAIL_PORT`       | Porta SMTP                      | 465             |
| `MAIL_FROM_NAME`  | Nome do remetente               | Forma Muana     |
| `MAIL_FROM_EMAIL` | Email do remetente              | —               |

## Dependências

| Pacote                  | Versão | Uso                         |
|-------------------------|--------|-----------------------------|
| `phpmailer/phpmailer`   | ^7.0   | Envio de e-mail via SMTP    |

Autoload via classmap: `core/`, `controllers/`, `models/`.

## Convenções do Código

- **Controllers**: `NomeController.php`, PascalCase, métodos camelCase
- **Models**: `Nome.php`, PascalCase — métodos `static` para buscas (`findById`, `findAll`), métodos de instância para persistência (`save`)
- **Views**: PHP puro, organizadas por domínio em `views/nome_dominio/`
- **Queries**: Sempre via PDO com prepared statements
- **Sanitização**: `htmlspecialchars()` para saída em HTML
- **Senhas**: `password_hash()` / `password_verify()` (bcrypt)
- **BASE_URL**: Usar a constante `BASE_URL` (definida em `helpers.php`) para links internos
