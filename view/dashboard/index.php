<?php $pageTitle = 'Dashboard — '.APP_NAME; ?>
<?php
function statusLabel(string $s): string {
    return ['aberto'=>'Aberto','em_andamento'=>'Em Andamento','aguardando'=>'Aguardando','resolvido'=>'Resolvido','fechado'=>'Fechado'][$s] ?? $s;
}
function statusClass(string $s): string {
    return 's-'.$s;
}
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
        <p>Bem-vindo, <?= htmlspecialchars(explode(' ', $usuarioSessao->nome)[0]) ?>! Aqui está o resumo do sistema.</p>
    </div>
    <a href="<?= APP_URL ?>/?c=chamados&a=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo Chamado
    </a>
</div>

<?php if ($slaVencidos > 0): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div>
        <strong><?= $slaVencidos ?> chamado<?= $slaVencidos > 1 ? 's' : '' ?> com SLA vencido!</strong>
        <a href="<?= APP_URL ?>/?c=chamados&a=index&sla_vencido=1" class="ms-2">Ver chamados →</a>
    </div>
</div>
<?php endif; ?>

<?php
$cards = [
    ['label'=>'Abertos',      'valor'=>$contagens['aberto'],       'icon'=>'bi-envelope-open',  'bg'=>'bg-primary bg-opacity-10', 'ic'=>'text-primary',  'link'=>'status=aberto'],
    ['label'=>'Em Andamento', 'valor'=>$contagens['em_andamento'], 'icon'=>'bi-arrow-repeat',   'bg'=>'bg-warning bg-opacity-10', 'ic'=>'text-warning',  'link'=>'status=em_andamento'],
    ['label'=>'Aguardando',   'valor'=>$contagens['aguardando'],   'icon'=>'bi-hourglass-split', 'bg'=>'bg-purple bg-opacity-10', 'ic'=>'text-purple',   'link'=>'status=aguardando'],
    ['label'=>'Resolvidos',   'valor'=>$contagens['resolvido'],    'icon'=>'bi-check-circle',   'bg'=>'bg-success bg-opacity-10', 'ic'=>'text-success',  'link'=>'status=resolvido'],
    ['label'=>'Total',        'valor'=>$total,                     'icon'=>'bi-ticket-detailed', 'bg'=>'bg-secondary bg-opacity-10','ic'=>'text-secondary','link'=>''],
];
?>
<div class="row g-3 mb-4">
<?php foreach ($cards as $card): ?>
    <div class="col-6 col-md-4 col-lg">
        <a href="<?= APP_URL ?>/?c=chamados&a=index<?= $card['link']?'&'.$card['link']:'' ?>" class="text-decoration-none">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon <?= $card['bg'] ?>">
                    <i class="<?= $card['icon'] ?> <?= $card['ic'] ?>"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1"><?= $card['valor'] ?></div>
                    <div class="text-muted small"><?= $card['label'] ?></div>
                </div>
            </div>
        </div>
        </a>
    </div>
<?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-lg-7">
        <div class="card-chart h-100">
            <div class="fw-semibold mb-3"><i class="bi bi-bar-chart me-1 text-primary"></i> Chamados por mês</div>
            <canvas id="chartMes" height="120"></canvas>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card-chart h-100 d-flex flex-column">
            <div class="fw-semibold mb-3"><i class="bi bi-pie-chart me-1 text-primary"></i> Por status</div>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                <canvas id="chartStatus" style="max-height:200px"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card-chart h-100">
            <div class="fw-semibold mb-3"><i class="bi bi-tags me-1 text-primary"></i> Por categoria</div>
            <canvas id="chartCategoria" height="160"></canvas>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card-chart h-100">
            <div class="fw-semibold mb-3"><i class="bi bi-flag me-1 text-primary"></i> Por prioridade</div>
            <canvas id="chartPrioridade" height="160"></canvas>
        </div>
    </div>
</div>

<div class="card table-card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold"><i class="bi bi-clock-history me-1 text-primary"></i> Chamados Recentes</span>
        <a href="<?= APP_URL ?>/?c=chamados&a=index" class="btn btn-sm btn-outline-primary">Ver todos</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Título</th><th>Status</th><th>Prioridade</th><th>SLA</th><th>Abertura</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($chamadosRecentes)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum chamado encontrado.</td></tr>
            <?php else: ?>
            <?php foreach ($chamadosRecentes as $c): ?>
                <tr>
                    <td><a href="<?= APP_URL ?>/?c=chamados&a=show&id=<?= $c->id ?>" class="fw-bold">#<?= $c->id ?></a></td>
                    <td>
                        <a href="<?= APP_URL ?>/?c=chamados&a=show&id=<?= $c->id ?>" class="text-decoration-none text-dark">
                            <?= htmlspecialchars($c->titulo) ?>
                        </a>
                        <?php if ($c->total_comentarios > 0): ?>
                        <span class="badge bg-light text-muted ms-1"><i class="bi bi-chat"></i> <?= $c->total_comentarios ?></span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge-status <?= statusClass($c->status) ?>"><?= statusLabel($c->status) ?></span></td>
                    <td>
                        <?php if ($c->nome_prioridade): ?>
                        <span style="color:<?= $c->cor_prioridade ?>" class="fw-semibold">
                            <i class="bi bi-circle-fill" style="font-size:.5rem;vertical-align:middle"></i>
                            <?= htmlspecialchars($c->nome_prioridade) ?>
                        </span>
                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($c->prazo_sla): ?>
                            <?php if ($c->sla_vencido): ?>
                            <span class="sla-vencido-badge"><i class="bi bi-alarm me-1"></i>Vencido</span>
                            <?php else: ?>
                            <span class="sla-ok-badge"><i class="bi bi-check me-1"></i>OK</span>
                            <?php endif; ?>
                        <?php else: ?><span class="text-muted small">—</span><?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= date('d/m H:i', strtotime($c->created_at)) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($logsRecentes)): ?>
<div class="card table-card">
    <div class="card-header bg-white py-3">
        <span class="fw-semibold"><i class="bi bi-shield-check me-1 text-primary"></i> Auditoria Recente</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr><th>Usuário</th><th>Ação</th><th>Chamado</th><th>Detalhes</th><th>Horário</th><th>IP</th></tr>
            </thead>
            <tbody>
            <?php foreach ($logsRecentes as $l): ?>
            <tr>
                <td class="small"><?= htmlspecialchars($l->nome_usuario) ?></td>
                <td><code class="small"><?= htmlspecialchars($l->acao) ?></code></td>
                <td><?= $l->id_chamado ? '<a href="'.APP_URL.'/?c=chamados&a=show&id='.$l->id_chamado.'">#'.$l->id_chamado.'</a>' : '—' ?></td>
                <td class="small text-muted"><?= htmlspecialchars($l->detalhes ?? '') ?></td>
                <td class="small text-muted"><?= date('d/m H:i', strtotime($l->created_at)) ?></td>
                <td class="small text-muted"><?= htmlspecialchars($l->ip ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php
$pageScript = "
(function(){
    criarGraficoBarras('chartMes',
        " . json_encode(array_column($porMes, 'mes_label')) . ",
        " . json_encode(array_map(fn($r)=>(int)$r->total, $porMes)) . ",
        '#4f46e5'
    );
    criarGraficoStatus('chartStatus',
        ['Aberto','Em Andamento','Aguardando','Resolvido','Fechado'],
        [" . implode(',', [$contagens['aberto'],$contagens['em_andamento'],$contagens['aguardando'],$contagens['resolvido'],$contagens['fechado']]) . "],
        ['#3b82f6','#f59e0b','#8b5cf6','#10b981','#6b7280']
    );
    criarGraficoPizza('chartCategoria',
        " . json_encode(array_column($porCategoria, 'nome')) . ",
        " . json_encode(array_map(fn($r)=>(int)$r->total, $porCategoria)) . ",
        " . json_encode(array_column($porCategoria, 'cor')) . "
    );
    criarGraficoStatus('chartPrioridade',
        " . json_encode(array_column($porPrioridade, 'nome')) . ",
        " . json_encode(array_map(fn($r)=>(int)$r->total, $porPrioridade)) . ",
        " . json_encode(array_column($porPrioridade, 'cor')) . "
    );
})();
";
?>
