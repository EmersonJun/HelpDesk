<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
<style>
.navbar-pub { background:#0f172a; }
.navbar-pub .nav-link { color:#94a3b8 !important; font-size:.9rem; }
.navbar-pub .nav-link:hover { color:#fff !important; }
.navbar-pub .nav-link.active { color:#818cf8 !important; font-weight:600; }
.navbar-pub .navbar-brand { color:#fff !important; font-weight:800; font-size:1.1rem; }
.hero { background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%); min-height:85vh;
        display:flex; align-items:center; }
.pub-card { border:none; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.08);
            transition:transform .2s; }
.pub-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,.12); }
.section-alt { background:#f8fafc; }
.badge-status-pub { font-size:.78rem; padding:4px 12px; border-radius:20px; font-weight:600; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-pub sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= APP_URL ?>/?c=publico&a=index">
            <i class="bi bi-headset me-2" style="color:#818cf8"></i>HelpDesk Pro
        </a>
        <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navPub">
            <i class="bi bi-list text-white fs-5"></i>
        </button>
        <div class="collapse navbar-collapse" id="navPub">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['a']??'index')==='index'?'active':'' ?>"
                       href="<?= APP_URL ?>/?c=publico&a=index">
                        <i class="bi bi-house me-1"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['a']??'')==='status'?'active':'' ?>"
                       href="<?= APP_URL ?>/?c=publico&a=status">
                        <i class="bi bi-activity me-1"></i>Status
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['a']??'')==='faq'?'active':'' ?>"
                       href="<?= APP_URL ?>/?c=publico&a=faq">
                        <i class="bi bi-question-circle me-1"></i>FAQ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['a']??'')==='contato'?'active':'' ?>"
                       href="<?= APP_URL ?>/?c=publico&a=contato">
                        <i class="bi bi-envelope me-1"></i>Contato
                    </a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <?php if ($usuarioSessao): ?>
                <a href="<?= APP_URL ?>/" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                </a>
                <a href="<?= APP_URL ?>/?c=auth&a=logout" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i>Sair
                </a>
                <?php else: ?>
                <a href="<?= APP_URL ?>/?c=auth&a=login" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
                <a href="<?= APP_URL ?>/?c=auth&a=registrar" class="btn btn-sm btn-primary">
                    <i class="bi bi-person-plus me-1"></i>Criar Conta
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
