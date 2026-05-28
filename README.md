<p align="center">
  <img src="banner.png" alt="Banner" width="75%">
</p>

# HelpDesk Pro 🎫
Sistema de Gestão de Chamados Corporativos — PHP + MySQL + MVC

---

## ✅ Requisitos
- XAMPP (Apache + MySQL)
- PHP 8.1+
- Navegador moderno

---

## 🚀 Instalação em 4 passos

### 1. Copiar o projeto
Copie a pasta `helpdesk/` inteira para:
```
C:\xampp\htdocs\helpdesk\
```

### 2. Criar o banco de dados
- Abra o XAMPP e inicie **Apache** e **MySQL**
- Acesse: http://localhost/phpmyadmin
- Clique em **Importar** → selecione o arquivo `setup.sql`
- Clique em **Executar**

### 3. Ajustar configurações (se necessário)
Edite o arquivo `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');   // XAMPP padrão; tente 3307 se der erro
define('DB_NAME', 'helpdesk');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_URL', 'http://localhost/helpdesk');
```

### 4. Acessar o sistema
Abra: **http://localhost/helpdesk**

---

## 👥 Usuários de teste
Todos com senha: **password**

| E-mail                    | Perfil    | Acesso |
|---------------------------|-----------|--------|
| admin@helpdesk.com        | Admin     | Total  |
| atendente@helpdesk.com    | Atendente | Chamados + Kanban + Relatórios |
| cliente@helpdesk.com      | Cliente   | Apenas seus chamados |

---

## 🗂️ Estrutura MVC

```
helpdesk/
├── index.php              ← Front Controller (roteador)
├── setup.sql              ← Banco de dados completo
├── config/
│   ├── config.php         ← Configurações
│   └── database.php       ← Conexão PDO singleton
├── model/
│   ├── BaseModel.php      ← CRUD genérico
│   ├── ChamadoModel.php   ← Lógica principal
│   ├── UsuarioModel.php
│   └── Models.php         ← Categoria, Prioridade, Comentario, Anexo, Log, Notificacao
├── controller/
│   ├── BaseController.php ← render, redirect, flash, guards
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── ChamadoController.php
│   ├── AdminController.php
│   └── ApiController.php
├── view/
│   ├── auth/login.php
│   ├── partials/          ← header, footer
│   ├── dashboard/
│   ├── chamados/          ← index, create, show, edit, kanban
│   ├── admin/             ← usuarios, categorias, prioridades, relatorios
│   └── errors/            ← 403, 404
├── assets/
│   ├── css/app.css
│   └── js/app.js
└── uploads/               ← Arquivos enviados pelos usuários
```

---

## 🌟 Funcionalidades

- ✅ Login/logout com controle de sessão
- ✅ 3 perfis: Cliente, Atendente, Admin
- ✅ CRUD completo de chamados
- ✅ Categorias com ícones e cores
- ✅ Prioridades com SLA configurável
- ✅ Comentários públicos e notas internas
- ✅ Upload de anexos
- ✅ Dashboard com 4 gráficos Chart.js
- ✅ Board Kanban drag-and-drop (SortableJS)
- ✅ Histórico de alterações por chamado
- ✅ Sistema de auditoria completo
- ✅ Notificações em tempo real
- ✅ Filtros avançados por status/categoria/prioridade/SLA
- ✅ Alerta visual de SLA vencido
- ✅ Gestão de usuários (admin)
- ✅ Relatórios com gráficos
- ✅ API REST JSON (`/api`)
- ✅ Arquitetura MVC pura em PHP
- ✅ PDO com prepared statements (seguro contra SQL Injection)
- ✅ Bootstrap 5 + Bootstrap Icons

---

## 🔌 API REST

| Endpoint | Descrição |
|---|---|
| `/?c=api&a=chamados` | Lista chamados do usuário logado |
| `/?c=api&a=chamado&id=N` | Detalhe de um chamado |
| `/?c=api&a=estatisticas` | Estatísticas gerais (admin/atendente) |
| `/?c=api&a=notificacoes` | Notificações do usuário |

---

## 👨‍💻 Desenvolvido por

| Membros |
|---|
| Taynara Piloneto mafra | 
| Luiz Gustavo Amaral |
| Bruno kuntze | 
| Samanta Andrade |
| Emerson Junior |

## Projeto acadêmico — Desenvolvimento de Sistemas PHP
## Universidade Positivo — 2025
