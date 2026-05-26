<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="login-page">
<div class="login-card">
    <div class="text-center mb-4">
        <div class="login-logo"><i class="bi bi-headset"></i></div>
        <h4 class="fw-bold mb-0">HelpDesk Pro</h4>
        <p class="text-muted small">Sistema de Gestão de Chamados</p>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
    <?php $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <div class="alert alert-<?= $f['tipo'] ?> d-flex align-items-center gap-2 py-2 mb-3">
        <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($f['msg']) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($erro)): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
        <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($erro) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($_COOKIE['helpdesk_nome']) && empty($_POST)): ?>
    <div class="alert alert-info py-2 mb-3 small">
        <i class="bi bi-hand-wave me-1"></i>
        Bem-vindo(a) de volta, <strong><?= htmlspecialchars($_COOKIE['helpdesk_nome']) ?></strong>!
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/?c=auth&a=login">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold">E-mail</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control"
                       placeholder="seu@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? $_COOKIE['helpdesk_nome'] ?? '') ?>"
                       required autofocus>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Senha</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="senha" id="senhaInput"
                       class="form-control" placeholder="••••••••" required>
                <button type="button" class="btn btn-outline-secondary" onclick="toggleSenha()">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="lembrar" id="chkLembrar" value="1"
                       <?= isset($_COOKIE['helpdesk_remember']) ? 'checked' : '' ?>>
                <label class="form-check-label small" for="chkLembrar">
                    <i class="bi bi-cookie me-1 text-warning"></i>Lembrar de mim por 30 dias
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
        </button>
    </form>

    <div class="text-center mt-3">
        <span class="text-muted small">Não tem conta?</span>
        <a href="<?= APP_URL ?>/?c=auth&a=registrar" class="text-primary fw-semibold small ms-1">
            <i class="bi bi-person-plus me-1"></i>Criar conta grátis
        </a>
    </div>

    <div class="text-center mt-2">
        <a href="<?= APP_URL ?>/?c=publico&a=index" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Voltar ao site
        </a>
    </div>

    <hr class="my-3">
    <div class="text-center">
        <p class="text-muted small mb-2">
            <strong>Usuários de teste</strong>
            <span class="badge bg-secondary ms-1">senha: password</span>
        </p>
        <div class="d-flex flex-column gap-1">
            <button class="btn btn-outline-secondary btn-sm" onclick="preencher('admin@helpdesk.com')">
                <i class="bi bi-shield-fill me-1 text-danger"></i>Admin
            </button>
            <button class="btn btn-outline-secondary btn-sm" onclick="preencher('atendente@helpdesk.com')">
                <i class="bi bi-headset me-1 text-warning"></i>Atendente
            </button>
            <button class="btn btn-outline-secondary btn-sm" onclick="preencher('cliente@helpdesk.com')">
                <i class="bi bi-person me-1 text-info"></i>Cliente
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function preencher(email) {
    document.querySelector('[name=email]').value = email;
    document.querySelector('[name=senha]').value = 'password';
}
function toggleSenha() {
    const inp = document.getElementById('senhaInput');
    const ico = document.getElementById('eyeIcon');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
