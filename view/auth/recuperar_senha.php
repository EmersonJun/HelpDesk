<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="login-page">
<div class="login-card" style="max-width:460px">

    <div class="text-center mb-4">
        <div class="login-logo"><i class="bi bi-shield-lock"></i></div>
        <h5 class="fw-bold mb-0">Recuperar Senha</h5>
        <p class="text-muted small">HelpDesk Pro</p>
    </div>

    <div class="d-flex gap-2 mb-4">
        <div class="flex-fill rounded-pill" style="height:5px;background:<?= $etapa>=1?'#4f46e5':'#e2e8f0' ?>"></div>
        <div class="flex-fill rounded-pill" style="height:5px;background:<?= $etapa>=2?'#4f46e5':'#e2e8f0' ?>"></div>
    </div>

    <?php if (!empty($erro)): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
        <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($erro) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/?c=auth&a=recuperarSenha">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="etapa" value="<?= $etapa ?>">
        <?php if ($etapa === 2): ?>
        <input type="hidden" name="id_usuario" value="<?= $idUser ?>">
        <?php endif; ?>

        <?php if ($etapa === 1): ?>
        <p class="text-muted small mb-3">
            <span class="badge bg-primary me-1">1 de 2</span>
            Confirme sua identidade
        </p>
        <div class="mb-3">
            <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control"
                       placeholder="seu@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">CPF <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                <input type="text" name="cpf" class="form-control"
                       placeholder="000.000.000-00"
                       value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>"
                       oninput="mascaraCpf(this)" maxlength="14" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Data de Nascimento <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                <input type="date" name="data_nascimento" class="form-control"
                       value="<?= htmlspecialchars($_POST['data_nascimento'] ?? '') ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="bi bi-arrow-right me-1"></i> Verificar identidade
        </button>

        <?php else: ?>
        <p class="text-muted small mb-3">
            <span class="badge bg-success me-1">2 de 2</span>
            Identidade confirmada! Defina sua nova senha.
        </p>
        <div class="mb-3">
            <label class="form-label fw-semibold">Nova senha <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="nova_senha" id="novaSenha" class="form-control"
                       placeholder="Mínimo 6 caracteres" required oninput="verForça()">
                <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePwd('novaSenha',this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <div id="forca" style="height:4px;border-radius:2px;margin-top:4px;transition:all .3s"></div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Confirmar senha <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="confirmacao" id="confSenha" class="form-control"
                       placeholder="Repita a senha" required>
                <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePwd('confSenha',this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
            <i class="bi bi-check-circle me-1"></i> Salvar nova senha
        </button>
        <?php endif; ?>
    </form>

    <div class="text-center mt-4">
        <a href="<?= APP_URL ?>/?c=auth&a=login" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Voltar ao login
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function mascaraCpf(el) {
    let v = el.value.replace(/\D/g,'').substring(0,11);
    if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/,'$1.$2.$3-$4');
    else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/,'$1.$2.$3');
    else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/,'$1.$2');
    el.value = v;
}
function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.querySelector('i').className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
function verForça() {
    const s = document.getElementById('novaSenha').value;
    const bar = document.getElementById('forca');
    let score = 0;
    if (s.length >= 6)  score++;
    if (s.length >= 10) score++;
    if (/[A-Z]/.test(s)) score++;
    if (/[0-9]/.test(s)) score++;
    if (/[^a-zA-Z0-9]/.test(s)) score++;
    const cores = ['#ef4444','#f97316','#f59e0b','#10b981','#059669'];
    bar.style.width = (score * 20) + '%';
    bar.style.background = cores[score-1] || '#e2e8f0';
}
</script>
</body>
</html>
