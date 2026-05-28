CREATE DATABASE IF NOT EXISTS helpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE helpdesk;

CREATE TABLE IF NOT EXISTS usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100)  NOT NULL,
    email       VARCHAR(100)  NOT NULL UNIQUE,
    senha       VARCHAR(255)  NOT NULL,
    perfil      ENUM('cliente','atendente','admin') DEFAULT 'cliente',
    departamento VARCHAR(100),
    telefone    VARCHAR(20),
    cpf             VARCHAR(14),
    data_nascimento DATE,
    ativo       TINYINT(1)   DEFAULT 1,
    created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categorias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    descricao   TEXT,
    icone       VARCHAR(50)  DEFAULT 'bi-tag',
    cor         VARCHAR(20)  DEFAULT '#6c757d',
    ativo       TINYINT(1)  DEFAULT 1,
    created_at  DATETIME    DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS prioridades (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(50)  NOT NULL,
    cor         VARCHAR(20)  NOT NULL DEFAULT '#6c757d',
    sla_horas   INT          NOT NULL DEFAULT 24,
    nivel       INT          NOT NULL DEFAULT 1,
    created_at  DATETIME    DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS chamados (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    titulo          VARCHAR(200) NOT NULL,
    descricao       TEXT         NOT NULL,
    status          ENUM('aberto','em_andamento','aguardando','resolvido','fechado') DEFAULT 'aberto',
    id_usuario      INT          NOT NULL,
    id_categoria    INT,
    id_prioridade   INT,
    id_atendente    INT,
    prazo_sla       DATETIME,
    resolvido_em    DATETIME,
    avaliacao       TINYINT,
    created_at      DATETIME     DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario)    REFERENCES usuarios(id),
    FOREIGN KEY (id_categoria)  REFERENCES categorias(id),
    FOREIGN KEY (id_prioridade) REFERENCES prioridades(id),
    FOREIGN KEY (id_atendente)  REFERENCES usuarios(id)
);

ALTER TABLE chamados AUTO_INCREMENT = 100000;

-- Adicionar campos CPF e data_nascimento (execute se o banco já existir)
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS cpf VARCHAR(14) AFTER telefone;
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS data_nascimento DATE AFTER cpf;

CREATE TABLE IF NOT EXISTS comentarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_chamado  INT     NOT NULL,
    id_usuario  INT     NOT NULL,
    texto       TEXT    NOT NULL,
    interno     TINYINT(1) DEFAULT 0,
    created_at  DATETIME   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_chamado) REFERENCES chamados(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS anexos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    id_chamado      INT          NOT NULL,
    id_usuario      INT          NOT NULL,
    nome_original   VARCHAR(255) NOT NULL,
    nome_arquivo    VARCHAR(255) NOT NULL,
    tipo            VARCHAR(100),
    tamanho         INT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_chamado) REFERENCES chamados(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_chamado  INT,
    id_usuario  INT          NOT NULL,
    acao        VARCHAR(100) NOT NULL,
    detalhes    TEXT,
    ip          VARCHAR(45),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_chamado) REFERENCES chamados(id) ON DELETE SET NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS notificacoes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario  INT  NOT NULL,
    id_chamado  INT,
    mensagem    TEXT NOT NULL,
    lida        TINYINT(1) DEFAULT 0,
    tipo        VARCHAR(30) DEFAULT 'info',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_chamado) REFERENCES chamados(id) ON DELETE SET NULL
);

INSERT INTO usuarios (nome, email, senha, perfil, departamento, telefone) VALUES
('Admin Sistema',    'admin@helpdesk.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',     'TI',      '(41) 9999-0001'),
('Carlos Atendente', 'atendente@helpdesk.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'atendente', 'Suporte',  '(41) 9999-0002'),
('Maria Cliente',    'cliente@helpdesk.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente',   'Financeiro','(41) 9999-0003'),
('João Cliente',     'joao@helpdesk.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente',   'RH',        '(41) 9999-0004');

INSERT INTO categorias (nome, descricao, icone, cor) VALUES
('Hardware',   'Problemas com equipamentos físicos',       'bi-pc-display',   '#dc3545'),
('Software',   'Problemas com programas e sistemas',       'bi-code-square',  '#0d6efd'),
('Rede',       'Problemas de conectividade e rede',        'bi-wifi',         '#fd7e14'),
('Acesso',     'Solicitações de acesso e permissões',      'bi-key',          '#6f42c1'),
('Impressora', 'Problemas com impressoras e periféricos',  'bi-printer',      '#20c997'),
('Outros',     'Outros tipos de solicitação',              'bi-three-dots',   '#6c757d');

INSERT INTO prioridades (nome, cor, sla_horas, nivel) VALUES
('Crítica', '#dc3545', 4,  4),
('Alta',    '#fd7e14', 8,  3),
('Média',   '#ffc107', 24, 2),
('Baixa',   '#28a745', 72, 1);

INSERT INTO chamados (titulo, descricao, status, id_usuario, id_categoria, id_prioridade, id_atendente, prazo_sla, created_at) VALUES
('Computador não liga',          'Meu computador não está ligando desde ontem de manhã. Já verifiquei a tomada e o cabo.', 'aberto',        3, 1, 2, NULL, DATE_ADD(NOW(), INTERVAL 8  HOUR), DATE_SUB(NOW(), INTERVAL 2 HOUR)),
('Sem acesso ao sistema de RH',  'Desde ontem não consigo acessar o sistema de RH. Aparece erro de autenticação.',       'em_andamento',  3, 4, 3, 2,    DATE_ADD(NOW(), INTERVAL 24 HOUR), DATE_SUB(NOW(), INTERVAL 5 HOUR)),
('Internet lenta na sala 302',   'A internet está muito lenta no terceiro andar, sala 302. Afeta toda a equipe.',        'resolvido',     4, 3, 4, 2,    DATE_SUB(NOW(), INTERVAL 48 HOUR), DATE_SUB(NOW(), INTERVAL 3 DAY)),
('Impressora não imprime',       'A impressora da recepção parou de funcionar. Aparece erro de papel mas a bandeja está cheia.', 'em_andamento', 4, 5, 3, 2, DATE_ADD(NOW(), INTERVAL 20 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY)),
('Atualizar Excel',              'Preciso atualizar o Microsoft Excel para a versão mais recente.',                      'aguardando',    3, 2, 4, 2,    DATE_ADD(NOW(), INTERVAL 60 HOUR), DATE_SUB(NOW(), INTERVAL 6 HOUR)),
('Vírus no computador',          'Meu computador está com comportamento estranho, acredito que seja vírus.',             'aberto',        4, 1, 1, NULL, DATE_ADD(NOW(), INTERVAL 4  HOUR), DATE_SUB(NOW(), INTERVAL 30 MINUTE)),
('Criar usuário no sistema',     'Preciso criar acesso para o novo funcionário João Paulo, do setor financeiro.',        'fechado',       3, 4, 2, 2,    DATE_SUB(NOW(), INTERVAL 72 HOUR), DATE_SUB(NOW(), INTERVAL 5 DAY)),
('Notebook não carrega',         'A bateria do meu notebook não carrega mais. Testei com outro carregador e o problema persiste.','aberto', 3, 1, 3, NULL, DATE_ADD(NOW(), INTERVAL 22 HOUR), DATE_SUB(NOW(), INTERVAL 1 HOUR));

INSERT INTO comentarios (id_chamado, id_usuario, texto, interno, created_at) VALUES
(2, 2, 'Olá Maria! Recebi seu chamado e já estou verificando. Vou precisar de acesso remoto ao seu computador.',      0, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(2, 3, 'Tudo bem, pode fazer o acesso remoto quando quiser. Estou no escritório.',                                     0, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(2, 2, 'NOTA INTERNA: Problema parece ser com o AD. Vou escalar para o time de infra.',                               1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(3, 2, 'Identificamos o problema: havia um cabo danificado no switch do 3º andar. Já foi substituído.',                0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 4, 'Perfeito! Realmente voltou a funcionar. Obrigado pela rapidez!',                                               0, DATE_SUB(NOW(), INTERVAL 2 DAY));

INSERT INTO logs (id_chamado, id_usuario, acao, detalhes, ip, created_at) VALUES
(1, 3, 'CHAMADO_CRIADO',   'Chamado aberto pelo cliente',                        '127.0.0.1', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 3, 'CHAMADO_CRIADO',   'Chamado aberto pelo cliente',                        '127.0.0.1', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(2, 2, 'STATUS_ALTERADO',  'Status: aberto → em_andamento',                     '127.0.0.1', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(3, 2, 'STATUS_ALTERADO',  'Status: em_andamento → resolvido',                  '127.0.0.1', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 1, 'LOGIN',            'Login realizado com sucesso',                         '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 HOUR));

INSERT INTO notificacoes (id_usuario, id_chamado, mensagem, lida, tipo) VALUES
(2, 1, 'Novo chamado aberto: Computador não liga',           0, 'info'),
(2, 6, 'Novo chamado urgente: Vírus no computador',          0, 'danger'),
(3, 2, 'Seu chamado #2 foi atualizado para: em andamento',   0, 'success'),
(4, 3, 'Seu chamado #3 foi resolvido!',                      1, 'success');
