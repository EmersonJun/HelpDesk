<?php
abstract class BaseController {

    protected function render(string $view, array $data = []): void {
        extract($data);
        $pageTitle     = $pageTitle ?? APP_NAME;
        $usuarioSessao = $this->user();
        $csrf_token    = $this->gerarTokenCSRF(); 
        include ROOT . '/view/partials/header.php';
        include ROOT . "/view/{$view}.php";
        include ROOT . '/view/partials/footer.php';
    }

    protected function renderPublica(string $view, array $data = []): void {
        extract($data);
        $pageTitle     = $pageTitle ?? APP_NAME;
        $usuarioSessao = $this->user();
        $csrf_token    = $this->gerarTokenCSRF(); 
        include ROOT . '/view/partials/header_publico.php';
        include ROOT . "/view/{$view}.php";
        include ROOT . '/view/partials/footer_publico.php';
    }

    protected function json(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function redirect(string $url): void {
        header("Location: $url"); exit;
    }

    protected function flash(string $tipo, string $msg): void {
        $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $msg];
    }

    protected function getFlash(): ?array {
        $f = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $f;
    }

    protected function user(): ?object {
        $raw = $_SESSION['usuario'] ?? null;
        if ($raw === null)   return null;
        if (is_object($raw)) return $raw;
        if (is_array($raw))  return (object)$raw;
        return null;
    }

    protected function sessaoRequerida(): void {
        if ($this->user() === null) {
            $this->redirect(APP_URL . '/?c=auth&a=login');
        }
    }

    protected function perfilRequerido(string|array $perfis): void {
        $this->sessaoRequerida();
        $u = $this->user();
        if (!in_array($u->perfil, (array)$perfis)) {
            include ROOT . '/view/errors/403.php'; exit;
        }
    }

    protected function gerarTokenCSRF(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validarCSRF(): void {
        $tokenEnviado  = $_POST['csrf_token'] ?? '';
        $tokenSessao   = $_SESSION['csrf_token'] ?? '';

        if (!$tokenSessao || !hash_equals($tokenSessao, $tokenEnviado)) {
            http_response_code(403);
            die('<h2 style="font-family:sans-serif;color:#c00;padding:20px">
                 ⚠️ Token CSRF inválido. Recarregue a página e tente novamente.</h2>');
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    protected function campoCSRF(): string {
        $token = $this->gerarTokenCSRF();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    protected function salvarCookieLembrar(object $usuario): void {
        $payload = base64_encode(json_encode([
            'id'    => $usuario->id,
            'email' => $usuario->email,
            'hash'  => hash_hmac('sha256', $usuario->email . $usuario->senha, COOKIE_SECRET),
        ]));
        setcookie('helpdesk_remember', $payload, [
            'expires'  => time() + (30 * 24 * 3600), 
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    protected function limparCookieLembrar(): void {
        setcookie('helpdesk_remember', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    protected function tentarLoginPorCookie(): bool {
        if ($this->user() !== null) return true; // já logado

        $cookie = $_COOKIE['helpdesk_remember'] ?? null;
        if (!$cookie) return false;

        try {
            $dados = json_decode(base64_decode($cookie), true);
            if (!$dados || !isset($dados['id'], $dados['email'], $dados['hash'])) return false;

            $um = new UsuarioModel();
            $u  = $um->buscarPorEmail($dados['email']);
            if (!$u) return false;

            $hashEsperado = hash_hmac('sha256', $u->email . $u->senha, COOKIE_SECRET);
            if (!hash_equals($hashEsperado, $dados['hash'])) return false;

            $_SESSION['usuario'] = (array)$u;
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
