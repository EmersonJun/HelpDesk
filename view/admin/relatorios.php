<?php $pageTitle = 'Relatórios — '.APP_NAME;
$total = array_sum((array)$contagens);
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-bar-chart-line me-2 text-primary"></i>Relatórios & Estatísticas</h4>
        <p>Visão geral do desempenho do suporte.</p>
    </div>
</div>
x
<div class="row g-3 mb-4">
    <?php
    $resumo = [
        ['Abertos',       $contagens['aberto'],       '#3b82f6','bi-envelope-open'],
        ['Em Andamento',  $contagens['em_andamento'],  '#f59e0b','bi-arrow-repeat'],
        ['Aguardando',    $contagens['aguardando'],    '#8b5cf6','bi-hourglass'],
        ['Resolvidos',    $contagens['resolvido'],     '#10b981','bi-check-circle'],
        ['Fechados',      $contagens['fechado'],       '#6b7280','bi-lock'],
        ['Total',         $total,                      '#4f46e5','bi-ticket-detailed'],
        ['SLA Vencidos',  $slaVencidos,                '#dc3545','bi-alarm'],
    ];
    foreach ($resumo as [$label, $val, $cor, $icon]):
    ?>
    <div class="col-6 col-md-4 col-lg-3 col-xl">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid <?= $cor ?> !important">
            <div class="card-body py-3">
                <div class="text-muted small mb-1"><i class="<?= $icon ?> me-1"></i><?= $label ?></div>
                <div class="fw-bold fs-3" style="color:<?= $cor ?>"><?= $val ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-lg-8">
        <div class="card-chart">
            <div class="fw-semibold mb-3"><i class="bi bi-bar-chart me-1 text-primary"></i> Chamados por mês (últimos 12 meses)</div>
            <canvas id="chartMesRel" height="100"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card-chart h-100">
            <div class="fw-semibold mb-3"><i class="bi bi-pie-chart me-1 text-primary"></i> Por status</div>
            <canvas id="chartStatusRel" style="max-height:220px"></canvas>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card-chart">
            <div class="fw-semibold mb-3"><i class="bi bi-tags me-1 text-primary"></i> Por categoria</div>
            <canvas id="chartCatRel" height="160"></canvas>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card-chart">
            <div class="fw-semibold mb-3"><i class="bi bi-flag me-1 text-primary"></i> Por prioridade</div>
            <canvas id="chartPriRel" height="160"></canvas>
        </div>
    </div>
</div>

<div class="card table-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold"><i class="bi bi-shield-check me-1 text-primary"></i> Log de Auditoria</span>
        <span class="badge bg-secondary"><?= count($logs) ?> registros</span>
    </div>
    <div class="table-responsive" style="max-height:400px;overflow-y:auto">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light sticky-top">
                <tr><th>Data/Hora</th><th>Usuário</th><th>Ação</th><th>Chamado</th><th>Detalhes</th><th>IP</th></tr>
            </thead>
            <tbody>
            <?php foreach($logs as $l): ?>
            <tr>
                <td class="small text-muted text-nowrap"><?= date('d/m/Y H:i:s', strtotime($l->created_at)) ?></td>
                <td class="small fw-semibold"><?= htmlspecialchars($l->nome_usuario) ?></td>
                <td><code class="small"><?= htmlspecialchars($l->acao) ?></code></td>
                <td>
                    <?php if ($l->id_chamado): ?>
                    <a href="<?= APP_URL ?>/?c=chamados&a=show&id=<?= $l->id_chamado ?>" class="small">#<?= $l->id_chamado ?></a>
                    <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                </td>
                <td class="small text-muted"><?= htmlspecialchars($l->detalhes ?? '') ?></td>
                <td class="small text-muted"><?= htmlspecialchars($l->ip ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$pageScript = "
(function(){
    criarGraficoBarras('chartMesRel',
        " . json_encode(array_column($porMes,'mes_label')) . ",
        " . json_encode(array_map(fn($r)=>(int)$r->total, $porMes)) . ",
        '#4f46e5'
    );
    criarGraficoStatus('chartStatusRel',
        ['Aberto','Em Andamento','Aguardando','Resolvido','Fechado'],
        [" . implode(',', array_values($contagens)) . "],
        ['#3b82f6','#f59e0b','#8b5cf6','#10b981','#6b7280']
    );
    criarGraficoPizza('chartCatRel',
        " . json_encode(array_column($porCategoria,'nome')) . ",
        " . json_encode(array_map(fn($r)=>(int)$r->total, $porCategoria)) . ",
        " . json_encode(array_column($porCategoria,'cor')) . "
    );
    criarGraficoStatus('chartPriRel',
        " . json_encode(array_column($porPrioridade,'nome')) . ",
        " . json_encode(array_map(fn($r)=>(int)$r->total, $porPrioridade)) . ",
        " . json_encode(array_column($porPrioridade,'cor')) . "
    );
})();
";
?>
