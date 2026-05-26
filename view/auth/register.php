<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Criar Conta' ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
<style>
  .register-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
    padding: 24px;
  }
  .register-card {
    background: #fff;
    border-radius: 16px;
    padding: 36px 40px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
  }
  .register-logo { font-size: 2rem; font-weight: 800; color: #4f46e5; }
  .step-bar { display: flex; gap: 4px; margin-bottom: 24px; }
  .step-bar span {
    flex: 1; height: 4px; border-radius: 2px; background: #e2e8f0;
    transition: background .3s;
  }
  .step-bar span.active { background: #4f46e5; }
  .field-group { display: none; }
  .field-group.active { display: block; }
  .password-strength { height: 4px; border-radius: 2px; transition: all .3s; margin-top: 4px; }
  .req-item { font-size: .8rem; color: #94a3b8; transition: color .2s; }
  .req-item.ok { color: #10b981; }
  .req-item.ok::before { content: '✓ '; }
  .req-item:not(.ok)::before { content: '○ '; }
</style>
</head>
<body class="register-page">

<div class="register-card">
  <div class="text-center mb-3">
    <div class="register-logo"><i class="bi bi-headset"></i></div>
    <h5 class="fw-bold mb-0 mt-1">Criar Conta</h5>
    <p class="text-muted small">HelpDesk Pro — Acesso gratuito como cliente</p>
  </div>

  <div class="step-bar" id="stepBar">
    <span class="active" id="step1bar"></span>
    <span id="step2bar"></span>
    <span id="step3bar"></span>
  </div>

  <?php if (!empty($erro)): ?>
  <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
    <i class="bi bi-exclamation-circle-fill"></i>
    <?= htmlspecialchars($erro) ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/?c=auth&a=salvarRegistro" id="registerForm" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $token ?? '' ?>">

    <div class="field-group active" id="etapa1">
      <p class="text-muted small mb-3">
        <span class="badge bg-primary me-1">1 de 3</span> Dados pessoais
      </p>
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Nome completo <span class="text-danger">*</span>
        </label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" name="nome" id="fNome" class="form-control"
                 placeholder="Seu nome completo"
                 value="<?= htmlspecialchars($dados['nome'] ?? '') ?>"
                 autocomplete="name">
        </div>
        <div class="invalid-feedback d-block" id="erroNome"></div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">
          E-mail <span class="text-danger">*</span>
        </label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" id="fEmail" class="form-control"
                 placeholder="seu@email.com"
                 value="<?= htmlspecialchars($dados['email'] ?? '') ?>"
                 autocomplete="email">
        </div>
        <div class="invalid-feedback d-block" id="erroEmail"></div>
      </div>
      <button type="button" class="btn btn-primary w-100 py-2 fw-semibold"
              onclick="irEtapa(2)">
        Continuar <i class="bi bi-arrow-right ms-1"></i>
      </button>
    </div>

    <div class="field-group" id="etapa2">
      <p class="text-muted small mb-3">
        <span class="badge bg-primary me-1">2 de 3</span> Crie sua senha
      </p>
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Senha <span class="text-danger">*</span>
        </label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="senha" id="fSenha" class="form-control"
                 placeholder="Mínimo 6 caracteres" autocomplete="new-password"
                 oninput="verificarSenha()">
          <button type="button" class="btn btn-outline-secondary" id="btnVerSenha"
                  onclick="toggleSenha('fSenha','btnVerSenha')">
            <i class="bi bi-eye"></i>
          </button>
        </div>
        <div class="password-strength mt-2" id="strengthBar"></div>
        <div id="strengthLabel" class="text-muted" style="font-size:.75rem;margin-top:3px"></div>
        <div class="mt-2 d-flex flex-column gap-1">
          <div class="req-item" id="req6">Mínimo 6 caracteres</div>
          <div class="req-item" id="reqUpper">Letra maiúscula</div>
          <div class="req-item" id="reqNum">Número</div>
        </div>
        <div class="invalid-feedback d-block" id="erroSenha"></div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Confirmar senha <span class="text-danger">*</span>
        </label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
          <input type="password" name="confirmacao" id="fConf" class="form-control"
                 placeholder="Repita a senha" autocomplete="new-password"
                 oninput="verificarConfirmacao()">
          <button type="button" class="btn btn-outline-secondary"
                  onclick="toggleSenha('fConf',this)">
            <i class="bi bi-eye"></i>
          </button>
        </div>
        <div class="invalid-feedback d-block" id="erroConf"></div>
      </div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary flex-fill"
                onclick="irEtapa(1)">
          <i class="bi bi-arrow-left me-1"></i> Voltar
        </button>
        <button type="button" class="btn btn-primary flex-fill py-2 fw-semibold"
                onclick="irEtapa(3)">
          Continuar <i class="bi bi-arrow-right ms-1"></i>
        </button>
      </div>
    </div>

    <div class="field-group" id="etapa3">
      <p class="text-muted small mb-3">
        <span class="badge bg-secondary me-1">3 de 3</span>
        Dados opcionais <span class="text-muted">(pode pular)</span>
      </p>
      <div class="mb-3">
        <label class="form-label fw-semibold">Departamento</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-building"></i></span>
          <input type="text" name="departamento" class="form-control"
                 placeholder="Ex: Financeiro, RH, TI…"
                 value="<?= htmlspecialchars($dados['departamento'] ?? '') ?>">
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Telefone</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-telephone"></i></span>
          <input type="text" name="telefone" class="form-control"
                 placeholder="(41) 9xxxx-xxxx"
                 value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>"
                 oninput="mascaraTelefone(this)">
        </div>
      </div>

      <div class="bg-light rounded p-3 mb-3 small">
        <div class="fw-semibold mb-1 text-muted">Resumo do cadastro:</div>
        <div><i class="bi bi-person me-1 text-primary"></i>
          <span id="resumoNome" class="fw-semibold"></span></div>
        <div><i class="bi bi-envelope me-1 text-primary"></i>
          <span id="resumoEmail"></span></div>
        <div><i class="bi bi-shield-check me-1 text-success"></i>
          Perfil: <span class="badge bg-info text-dark">Cliente</span></div>
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary flex-fill"
                onclick="irEtapa(2)">
          <i class="bi bi-arrow-left me-1"></i> Voltar
        </button>
        <button type="submit" class="btn btn-success flex-fill py-2 fw-bold"
                id="btnCriar">
          <i class="bi bi-check-circle me-1"></i> Criar minha conta
        </button>
      </div>
    </div>

  </form>

  <div class="text-center mt-4">
    <span class="text-muted small">Já tem conta?</span>
    <a href="<?= APP_URL ?>/?c=auth&a=login"
       class="text-primary fw-semibold small ms-1">Fazer login</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let etapaAtual = 1;

  function irEtapa(n) {
    if (n > etapaAtual && !validarEtapa(etapaAtual)) return;

    document.getElementById('etapa' + etapaAtual).classList.remove('active');
    document.getElementById('step' + etapaAtual + 'bar').classList.remove('active');

    etapaAtual = n;

    document.getElementById('etapa' + n).classList.add('active');
    for (let i = 1; i <= n; i++)
      document.getElementById('step' + i + 'bar').classList.add('active');

    if (n === 3) preencherResumo();
  }

  function validarEtapa(n) {
    if (n === 1) {
      const nome  = document.getElementById('fNome').value.trim();
      const email = document.getElementById('fEmail').value.trim();
      let ok = true;
      document.getElementById('erroNome').textContent  = '';
      document.getElementById('erroEmail').textContent = '';
      if (!nome)  { document.getElementById('erroNome').textContent  = 'Informe seu nome.'; ok = false; }
      if (!email) { document.getElementById('erroEmail').textContent = 'Informe seu e-mail.'; ok = false; }
      else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
        { document.getElementById('erroEmail').textContent = 'E-mail inválido.'; ok = false; }
      return ok;
    }
    if (n === 2) {
      const s = document.getElementById('fSenha').value;
      const c = document.getElementById('fConf').value;
      let ok = true;
      document.getElementById('erroSenha').textContent = '';
      document.getElementById('erroConf').textContent  = '';
      if (s.length < 6) { document.getElementById('erroSenha').textContent = 'Mínimo 6 caracteres.'; ok = false; }
      if (s !== c)       { document.getElementById('erroConf').textContent  = 'As senhas não coincidem.'; ok = false; }
      return ok;
    }
    return true;
  }

  function verificarSenha() {
    const s    = document.getElementById('fSenha').value;
    const bar  = document.getElementById('strengthBar');
    const lbl  = document.getElementById('strengthLabel');
    const r6   = document.getElementById('req6');
    const rU   = document.getElementById('reqUpper');
    const rN   = document.getElementById('reqNum');

    r6.classList.toggle('ok', s.length >= 6);
    rU.classList.toggle('ok', /[A-Z]/.test(s));
    rN.classList.toggle('ok', /[0-9]/.test(s));

    let score = 0;
    if (s.length >= 6)  score++;
    if (s.length >= 10) score++;
    if (/[A-Z]/.test(s)) score++;
    if (/[0-9]/.test(s)) score++;
    if (/[^a-zA-Z0-9]/.test(s)) score++;

    const levels = [
      { w:'20%', bg:'#ef4444', txt:'Muito fraca' },
      { w:'40%', bg:'#f97316', txt:'Fraca' },
      { w:'60%', bg:'#f59e0b', txt:'Média' },
      { w:'80%', bg:'#10b981', txt:'Forte' },
      { w:'100%',bg:'#059669', txt:'Muito forte' },
    ];
    const lv = levels[Math.min(score, 4)];
    bar.style.width      = lv.w;
    bar.style.background = lv.bg;
    lbl.textContent      = s ? lv.txt : '';
    lbl.style.color      = lv.bg;
  }

  function verificarConfirmacao() {
    const s = document.getElementById('fSenha').value;
    const c = document.getElementById('fConf').value;
    const el= document.getElementById('erroConf');
    if (c && s !== c) el.textContent = 'As senhas não coincidem.';
    else el.textContent = '';
  }

  function toggleSenha(inputId, btn) {
    const inp = document.getElementById(inputId);
    const ico = btn.querySelector ? btn.querySelector('i') : btn;
    if (inp.type === 'password') {
      inp.type = 'text';
      ico.className = 'bi bi-eye-slash';
    } else {
      inp.type = 'password';
      ico.className = 'bi bi-eye';
    }
  }

  function mascaraTelefone(el) {
    let v = el.value.replace(/\D/g, '').substring(0, 11);
    if (v.length > 6) v = `(${v.slice(0,2)}) ${v.slice(2,7)}-${v.slice(7)}`;
    else if (v.length > 2) v = `(${v.slice(0,2)}) ${v.slice(2)}`;
    else if (v.length > 0) v = `(${v}`;
    el.value = v;
  }

  function preencherResumo() {
    document.getElementById('resumoNome').textContent  = document.getElementById('fNome').value;
    document.getElementById('resumoEmail').textContent = document.getElementById('fEmail').value;
  }

  document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnCriar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando conta…';
  });

  <?php if (!empty($erro)): ?>
  (function() {
    const erros = <?= json_encode($erro) ?>;
    if (erros.includes('senha') || erros.includes('coincidem') || erros.includes('caracteres')) {
      irEtapa(1); irEtapa(2);
    } else if (erros.includes('e-mail') || erros.includes('nome') || erros.includes('E-mail')) {
    }
  })();
  <?php endif; ?>
</script>
</body>
</html>
