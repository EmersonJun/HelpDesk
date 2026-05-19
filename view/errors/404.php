<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>404 — Não Encontrado</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
<div class="text-center p-5">
    <div class="display-1 text-muted mb-3"><i class="bi bi-search"></i></div>
    <h2 class="fw-bold mb-2">404 — Página não encontrada</h2>
    <p class="text-muted mb-4">O recurso que você procura não existe ou foi removido.</p>
    <a href="<?= defined('APP_URL') ? APP_URL.'/' : '/' ?>" class="btn btn-primary">
        <i class="bi bi-house me-1"></i> Voltar ao início
    </a>
</div>
</body></html>
