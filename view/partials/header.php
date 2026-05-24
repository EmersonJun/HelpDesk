<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="app-url" content="<?= APP_URL ?>">
<title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body>
<?php
$_dadosSessao  = $_SESSION['usuario'] ?? null;
$u             = is_array($_dadosSessao) ? (object)$_dadosSessao : $_dadosSessao;
$currentC      = $_GET['c'] ?? 'dashboard';
$currentA      = $_GET['a'] ?? 'index';
$totalNaoLidas = 0;
if ($u !== null && isset($u->id)) {
    $nm = new NotificacaoModel();
    $totalNaoLidas = $nm->contarNaoLidas((int)$u->id);
}
$isLogin = ($currentC === 'auth');
?>

<?php if ($u && !$isLogin): ?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-headset"></i> HelpDesk Pro
    </div>
    <nav>
        <div class="sidebar-section">Principal</div>
        <a href="<?= APP_URL ?>/"
           class="sidebar-link <?= $currentC==='dashboard'?'active':'' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="<?= APP_URL ?>/?c=chamados&a=index"
           class="sidebar-link <?= ($currentC==='chamados'&&$currentA==='index')?'active':'' ?>">
            <i class="bi bi-ticket-detailed"></i> Chamados
        </a>
        <a href="<?= APP_URL ?>/?c=chamados&a=create"
           class="sidebar-link <?= ($currentC==='chamados'&&$currentA==='create')?'active':'' ?>">
            <i class="bi bi-plus-circle"></i> Novo Chamado
        </a>
        <?php if (in_array($u->perfil, ['atendente','admin'])): ?>
        <a href="<?= APP_URL ?>/?c=chamados&a=kanban"
           class="sidebar-link <?= $currentA==='kanban'?'active':'' ?>">
            <i class="bi bi-kanban"></i> Kanban
        </a>
        <div class="sidebar-section">Relatórios</div>
        <a href="<?= APP_URL ?>/?c=admin&a=relatorios"
           class="sidebar-link <?= $currentA==='relatorios'?'active':'' ?>">
            <i class="bi bi-bar-chart-line"></i> Relatórios
        </a>
        <?php endif; ?>
        <?php if ($u->perfil === 'admin'): ?>
        <div class="sidebar-section">Administração</div>
        <a href="<?= APP_URL ?>/?c=admin&a=usuarios"
           class="sidebar-link <?= $currentA==='usuarios'?'active':'' ?>">
            <i class="bi bi-people"></i> Usuários
        </a>
        <a href="<?= APP_URL ?>/?c=admin&a=categorias"
           class="sidebar-link <?= $currentA==='categorias'?'active':'' ?>">
            <i class="bi bi-tags"></i> Categorias
        </a>
        <a href="<?= APP_URL ?>/?c=admin&a=prioridades"
           class="sidebar-link <?= $currentA==='prioridades'?'active':'' ?>">
            <i class="bi bi-exclamation-triangle"></i> Prioridades SLA
        </a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <i class="bi bi-person-circle me-1"></i>
        <strong><?= htmlspecialchars($u->nome) ?></strong><br>
        <span class="badge mt-1 <?= $u->perfil==='admin'?'bg-danger':($u->perfil==='atendente'?'bg-warning text-dark':'bg-info text-dark') ?>">
            <?= ucfirst($u->perfil) ?>
        </span>
        <?php if (!empty($u->departamento)): ?>
        <br><small><?= htmlspecialchars($u->departamento) ?></small>
        <?php endif; ?>
    </div>
</div>

<div class="page-content" id="pageContent">
    <div class="topbar">
       
        <span class="fw-bold text-primary d-lg-none">HelpDesk Pro</span>

        <div class="ms-auto d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary position-relative"
                        id="btnNotif" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-6"></i>
                    <?php if ($totalNaoLidas > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size:.6rem">
                        <?= $totalNaoLidas ?>
                    </span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0 notif-menu" style="width:320px">
                    <div class="px-3 py-2 border-bottom fw-semibold d-flex justify-content-between align-items-center">
                        <span>Notificações</span>
                        <?php if ($totalNaoLidas > 0): ?>
                        <span class="badge bg-danger"><?= $totalNaoLidas ?></span>
                        <?php endif; ?>
                    </div>
                    <div id="notif-list">
                        <div class="text-center p-3 text-muted small">Clique para carregar…</div>
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                        data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars(explode(' ', $u->nome)[0]) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header"><?= htmlspecialchars($u->nome) ?></h6></li>
                    <li><span class="dropdown-item-text text-muted small">
                        <?= htmlspecialchars($u->email) ?>
                    </span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger"
                           href="<?= APP_URL ?>/?c=auth&a=logout">
                        <i class="bi bi-box-arrow-right me-2"></i>Sair
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main-content">
<?php endif; ?>
