<?php
class AuthController extends BaseController {
    private UsuarioModel $usuarios;
    private LogModel $logs;

    public function __construct() {
        $this->usuarios = new UsuarioModel();
        $this->logs     = new LogModel();
    }

    public function login(): void {
        if ($this->tentarLoginPorCookie()) {
            $this->redirect(APP_URL . '/');
        }

        if (!empty($_SESSION['usuario'])) {
            $this->redirect(APP_URL . '/');
        }

        $erro  = null;
        $token = $this->gerarTokenCSRF();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validarCSRF();

            $email   = trim($_POST['email'] ?? '');
            $senha   = $_POST['senha'] ?? '';
            $lembrar = isset($_POST['lembrar']);

            if (!$email || !$senha) {
                $erro = 'Preencha e-mail e senha.';
            } else {
                $u = $this->usuarios->buscarPorEmail($email);
                if ($u && password_verify($senha, $u->senha)) {
                    $_SESSION['usuario'] = (array)$u;

                    if ($lembrar) {
                        $this->salvarCookieLembrar($u);
                        setcookie('helpdesk_nome', $u->nome, [
                            'expires'  => time() + (30 * 24 * 3600),
                            'path'     => '/',
                            'httponly' => false,
                            'samesite' => 'Strict',
                        ]);
                    }

                    $this->logs->registrar($u->id, 'LOGIN', null,
                        'Login realizado' . ($lembrar ? ' (lembrar ativo)' : ''));
                    $this->redirect(APP_URL . '/');
                } else {
                    $erro = 'E-mail ou senha inválidos.';
                    if ($u) $this->logs->registrar($u->id, 'LOGIN_FALHOU', null, 'Senha errada');
                }
            }
        }

        $pageTitle = 'Login — ' . APP_NAME;
        include ROOT . '/view/auth/login.php';
    }

    public function logout(): void {
        if ($u = $this->user()) {
            $this->logs->registrar($u->id, 'LOGOUT');
        }
        $this->limparCookieLembrar();
        setcookie('helpdesk_nome', '', ['expires' => time()-3600, 'path' => '/']);
        session_destroy();
        $this->redirect(APP_URL . '/?c=auth&a=login');
    }

    public function registrar(): void {
        if ($this->tentarLoginPorCookie()) {
            $this->redirect(APP_URL . '/');
        }
        if (!empty($_SESSION['usuario'])) {
            $this->redirect(APP_URL . '/');
        }
        $erro  = null;
        $dados = [];
        $token = $this->gerarTokenCSRF();
        $pageTitle = 'Criar Conta — ' . APP_NAME;
        include ROOT . '/view/auth/register.php';
    }

    public function salvarRegistro(): void {
        if (!empty($_SESSION['usuario'])) {
            $this->redirect(APP_URL . '/');
        }

        $this->validarCSRF();

        $nome           = trim($_POST['nome']            ?? '');
        $email          = trim($_POST['email']           ?? '');
        $senha          = $_POST['senha']                ?? '';
        $confirmacao    = $_POST['confirmacao']          ?? '';
        $departamento   = trim($_POST['departamento']    ?? '');
        $telefone       = trim($_POST['telefone']        ?? '');
        $cpf            = trim($_POST['cpf']             ?? '');
        $dataNascimento = trim($_POST['data_nascimento'] ?? '');

        $dados = compact('nome', 'email', 'departamento', 'telefone', 'cpf', 'data_nascimento');
        $dados['data_nascimento'] = $dataNascimento;
        $erro  = null;
        $token = $this->gerarTokenCSRF();

        if (!$nome || !$email || !$senha || !$confirmacao) {
            $erro = 'Preencha todos os campos obrigatórios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Formato de e-mail inválido.';
        } elseif (strlen($senha) < 6) {
            $erro = 'A senha deve ter no mínimo 6 caracteres.';
        } elseif ($senha !== $confirmacao) {
            $erro = 'As senhas não coincidem.';
        } elseif ($this->usuarios->emailExiste($email)) {
            $erro = 'Este e-mail já está cadastrado.';
        }

        if ($erro) {
            $pageTitle = 'Criar Conta — ' . APP_NAME;
            include ROOT . '/view/auth/register.php';
            return;
        }

        $id = $this->usuarios->criar([
            'nome'         => htmlspecialchars($nome),
            'email'        => $email,
            'senha'        => password_hash($senha, PASSWORD_DEFAULT),
            'perfil'       => 'cliente',
            'departamento'    => htmlspecialchars($departamento),
            'telefone'        => htmlspecialchars($telefone),
            'cpf'             => $cpf,
            'data_nascimento' => $dataNascimento ?: null,
            'ativo'           => 1,
        ]);

        $this->logs->registrar($id, 'REGISTRO', null, "Nova conta: {$email}");

        $u = $this->usuarios->buscarPorEmail($email);
        $_SESSION['usuario'] = (array)$u;

        $this->flash('success',
            'Conta criada com sucesso! Bem-vindo(a), ' . htmlspecialchars($nome) . '!');
        $this->redirect(APP_URL . '/');
    }

    public function recuperarSenha(): void {
        if (!empty($_SESSION['usuario'])) $this->redirect(APP_URL . '/');
        $erro    = null;
        $sucesso = false;
        $etapa   = 1;
        $idUser  = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validarCSRF();

            $etapa = (int)($_POST['etapa'] ?? 1);

            if ($etapa === 1) {
                $email      = trim($_POST['email']          ?? '');
                $cpf        = trim($_POST['cpf']            ?? '');
                $nascimento = trim($_POST['data_nascimento']?? '');

                if (!$email || !$cpf || !$nascimento) {
                    $erro = 'Preencha todos os campos.';
                } else {
                    $u = $this->usuarios->buscarParaRecuperacao($email, $cpf, $nascimento);
                    if ($u) {
                        $etapa  = 2;
                        $idUser = $u->id;
                        $_SESSION['recuperacao_id'] = $u->id;
                        $_SESSION['recuperacao_ts'] = time();
                    } else {
                        $erro = 'Dados não conferem. Verifique e-mail, CPF e data de nascimento.';
                    }
                }
            } elseif ($etapa === 2) {
                $idUser      = (int)($_SESSION['recuperacao_id'] ?? 0);
                $ts          = (int)($_SESSION['recuperacao_ts'] ?? 0);
                $novaSenha   = $_POST['nova_senha']    ?? '';
                $confirmacao = $_POST['confirmacao']   ?? '';

                if (!$idUser || (time() - $ts) > 600) {
                    $erro  = 'Sessão expirada. Tente novamente.';
                    $etapa = 1;
                } elseif (strlen($novaSenha) < 6) {
                    $erro = 'A senha deve ter no mínimo 6 caracteres.';
                } elseif ($novaSenha !== $confirmacao) {
                    $erro = 'As senhas não coincidem.';
                } else {
                    $this->usuarios->atualizarSenha($idUser, $novaSenha);
                    unset($_SESSION['recuperacao_id'], $_SESSION['recuperacao_ts']);
                    $this->flash('success', 'Senha redefinida com sucesso! Faça login com a nova senha.');
                    $this->redirect(APP_URL . '/?c=auth&a=login');
                }
            }
        }

        $token     = $this->gerarTokenCSRF();
        $pageTitle = 'Recuperar Senha — ' . APP_NAME;
        include ROOT . '/view/auth/recuperar_senha.php';
    }

}