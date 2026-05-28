<?php
class PublicoController extends BaseController {

    public function index(): void {
        $pageTitle = 'HelpDesk Pro — Suporte Corporativo';
        $this->renderPublica('publico/home', compact('pageTitle'));
    }

    public function faq(): void {
        $pageTitle = 'Perguntas Frequentes — HelpDesk Pro';
        $faqs = [
            ['p' => 'Como abro um chamado?',
             'r' => 'Crie uma conta ou faça login. No menu lateral, clique em "Novo Chamado", preencha o título, descrição, categoria e prioridade, e clique em "Abrir Chamado".'],
            ['p' => 'Quanto tempo leva para ser atendido?',
             'r' => 'O tempo de atendimento varia conforme a prioridade: Crítica (4h), Alta (8h), Média (24h) e Baixa (72h). Esses são os SLAs (acordos de nível de serviço).'],
            ['p' => 'Posso acompanhar meu chamado?',
             'r' => 'Sim! Após o login, acesse "Chamados" no menu para ver todos os seus chamados, o status atual, comentários do atendente e o histórico de alterações.'],
            ['p' => 'Como adiciono um arquivo ao chamado?',
             'r' => 'Na tela de abertura do chamado ou nos comentários, há um botão "Anexar" que permite enviar arquivos de até 10MB nos formatos: jpg, png, pdf, doc, xls, zip e txt.'],
            ['p' => 'O que significa cada status?',
             'r' => 'Aberto: aguardando atribuição. Em Andamento: sendo tratado. Aguardando: aguarda resposta. Resolvido: solução aplicada. Fechado: encerrado definitivamente.'],
            ['p' => 'Como faço para redefinir minha senha?',
             'r' => 'Entre em contato com o administrador do sistema pelo e-mail admin@helpdesk.com ou abra um chamado da categoria "Acesso" com a solicitação.'],
            ['p' => 'Meus dados são seguros?',
             'r' => 'Sim. As senhas são armazenadas com criptografia bcrypt, nunca em texto simples. A comunicação é protegida e os formulários possuem proteção CSRF.'],
            ['p' => 'Posso usar o sistema no celular?',
             'r' => 'Sim! O sistema é totalmente responsivo e funciona em smartphones e tablets com qualquer navegador moderno.'],
        ];
        $this->renderPublica('publico/faq', compact('pageTitle', 'faqs'));
    }

    public function status(): void {
        $pageTitle = 'Status do Sistema — HelpDesk Pro';

        try {
            $cm = new ChamadoModel();
            $contagens   = $cm->contarPorStatus();
            $slaVencidos = $cm->slaVencidos();
            $total       = array_sum($contagens);
            $sistemaOk   = true;
        } catch (\Throwable $e) {
            $contagens   = [];
            $slaVencidos = 0;
            $total       = 0;
            $sistemaOk   = false;
        }

        $servicos = [
            ['nome' => 'Servidor Web (Apache)',   'ok' => true],
            ['nome' => 'Banco de Dados (MySQL)',  'ok' => $sistemaOk],
            ['nome' => 'Sistema de Chamados',     'ok' => $sistemaOk],
            ['nome' => 'Upload de Arquivos',      'ok' => is_writable(UPLOAD_PATH)],
            ['nome' => 'Sistema de Notificações', 'ok' => $sistemaOk],
        ];

        $prioridades = (new PrioridadeModel())->listarOrdenadas();
        $this->renderPublica('publico/status',
            compact('pageTitle', 'contagens', 'slaVencidos', 'total', 'sistemaOk', 'prioridades'));
    }

    public function contato(): void {
        $pageTitle  = 'Contato — HelpDesk Pro';
        $enviado    = false;
        $erro       = null;
        $token      = $this->gerarTokenCSRF();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validarCSRF();

            $nome    = trim($_POST['nome']    ?? '');
            $email   = trim($_POST['email']   ?? '');
            $assunto = trim($_POST['assunto'] ?? '');
            $msg     = trim($_POST['mensagem']?? '');

            if (!$nome || !$email || !$assunto || !$msg) {
                $erro = 'Preencha todos os campos.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = 'E-mail inválido.';
            } else {
                setcookie('contato_nome', htmlspecialchars($nome), [
                    'expires'  => time() + (7 * 24 * 3600),
                    'path'     => '/',
                    'httponly' => false,
                    'samesite' => 'Strict',
                ]);
                $enviado = true;
            }
        }

        $nomeCookie = htmlspecialchars($_COOKIE['contato_nome'] ?? '');
        $token = $this->gerarTokenCSRF();

        $this->renderPublica('publico/contato',
            compact('pageTitle', 'enviado', 'erro', 'token', 'nomeCookie'));
    }
}
