<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Status do Sistema</h1>
        <p class="text-muted">Situação atual dos serviços em tempo real</p>
        <small class="text-muted"><i class="bi bi-clock me-1"></i>Atualizado em: <?= date('d/m/Y H:i:s') ?></small>
    </div>

    <div class="text-center mb-5">
        <?php if ($sistemaOk): ?>
        <div class="d-inline-flex align-items-center gap-2 px-4 py-3 rounded-pill"
             style="background:#d1fae5;color:#065f46;font-size:1.1rem;font-weight:600">
            <i class="bi bi-check-circle-fill fs-4"></i> Todos os sistemas operacionais
        </div>
        <?php else: ?>
        <div class="d-inline-flex align-items-center gap-2 px-4 py-3 rounded-pill"
             style="background:#fee2e2;color:#991b1b;font-size:1.1rem;font-weight:600">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i> Instabilidade detectada
        </div>
        <?php endif; ?>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold py-3">
                    <i class="bi bi-server me-2 text-primary"></i>Serviços
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach($servicos as $srv): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span><i class="bi bi-circle-fill me-2" style="font-size:.5rem;color:<?= $srv['ok']?'#10b981':'#ef4444' ?>"></i>
                            <?= htmlspecialchars($srv['nome']) ?>
                        </span>
                        <?php if ($srv['ok']): ?>
                        <span class="badge" style="background:#d1fae5;color:#065f46">Operacional</span>
                        <?php else: ?>
                        <span class="badge" style="background:#fee2e2;color:#991b1b">Indisponível</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($sistemaOk && $total > 0): ?>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h5 class="fw-bold mb-3 text-center">Chamados — Visão Geral</h5>
            <div class="row g-3">
                <?php foreach([
                    ['Abertos',      $contagens['aberto']??0,       '#3b82f6','bi-envelope-open'],
                    ['Em Andamento', $contagens['em_andamento']??0, '#f59e0b','bi-arrow-repeat'],
                    ['Resolvidos',   $contagens['resolvido']??0,    '#10b981','bi-check-circle'],
                    ['Total',        $total,                         '#4f46e5','bi-ticket-detailed'],
                ] as [$l,$v,$c,$i]): ?>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <i class="<?= $i ?> fs-3 mb-1" style="color:<?= $c ?>"></i>
                        <div class="fw-bold fs-3" style="color:<?= $c ?>"><?= $v ?></div>
                        <div class="text-muted small"><?= $l ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
