<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Nosso Suporte em Números</h1>
        <p class="text-muted">Transparência sobre o desempenho do nosso time de atendimento</p>
        <small class="text-muted">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Atualizado em: <?= date('d/m/Y \à\s H:i') ?>
        </small>
    </div>

    <?php if ($sistemaOk && $total > 0): ?>

    <div class="row g-4 mb-5">
        <?php
        $taxaResolucao = $total > 0
            ? round((($contagens['resolvido'] + $contagens['fechado']) / $total) * 100)
            : 0;
        $metricas = [
            ['bi-ticket-detailed',   '#4f46e5', 'Chamados Abertos',     $contagens['aberto']??0,      'aguardando atendimento'],
            ['bi-arrow-repeat',      '#f59e0b', 'Em Andamento',         $contagens['em_andamento']??0,'sendo atendidos agora'],
            ['bi-check-circle-fill', '#10b981', 'Resolvidos',           $contagens['resolvido']??0,   'no total'],
            ['bi-percent',           '#8b5cf6', 'Taxa de Resolução',    $taxaResolucao . '%',         'dos chamados resolvidos'],
        ];
        ?>
        <?php foreach($metricas as [$ico, $cor, $titulo, $valor, $sub]): ?>
        <div class="col-6 col-lg-3">
            <div class="pub-card card text-center p-4 h-100">
                <i class="<?= $ico ?> fs-2 mb-2" style="color:<?= $cor ?>"></i>
                <div class="fw-black display-6 mb-1" style="color:<?= $cor ?>"><?= $valor ?></div>
                <div class="fw-semibold"><?= $titulo ?></div>
                <div class="text-muted small"><?= $sub ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4 mb-5 justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-bar-chart me-2 text-primary"></i>
                    Distribuição por Status
                </h5>
                <?php
                $statusInfo = [
                    'aberto'       => ['Aberto',       '#3b82f6', $contagens['aberto']??0],
                    'em_andamento' => ['Em Andamento', '#f59e0b', $contagens['em_andamento']??0],
                    'aguardando'   => ['Aguardando',   '#8b5cf6', $contagens['aguardando']??0],
                    'resolvido'    => ['Resolvido',    '#10b981', $contagens['resolvido']??0],
                    'fechado'      => ['Fechado',      '#6b7280', $contagens['fechado']??0],
                ];
                foreach($statusInfo as [$label, $cor, $qtd]):
                    $pct = $total > 0 ? round(($qtd/$total)*100) : 0;
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold"><?= $label ?></span>
                        <span class="small text-muted"><?= $qtd ?> chamado<?= $qtd!=1?'s':'' ?> (<?= $pct ?>%)</span>
                    </div>
                    <div style="background:#f1f5f9;height:10px;border-radius:5px;overflow:hidden">
                        <div style="background:<?= $cor ?>;width:<?= $pct ?>%;height:10px;border-radius:5px;transition:width 1s ease"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-alarm me-2 text-primary"></i>
                    Nossos Compromissos de SLA
                </h5>
                <?php foreach($prioridades as $p): ?>
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded-2"
                     style="background:<?= $p->cor ?>12">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-inline-block"
                              style="width:10px;height:10px;background:<?= $p->cor ?>"></span>
                        <span class="fw-semibold small"><?= htmlspecialchars($p->nome) ?></span>
                    </div>
                    <span class="badge" style="background:<?= $p->cor ?>20;color:<?= $p->cor ?>;border:1px solid <?= $p->cor ?>40">
                        até <?= $p->sla_horas ?>h
                    </span>
                </div>
                <?php endforeach; ?>

                <?php if ($slaVencidos > 0): ?>
                <div class="alert alert-warning py-2 mt-3 small mb-0">
                    <i class="bi bi-alarm me-1"></i>
                    <strong><?= $slaVencidos ?></strong> chamado<?= $slaVencidos!=1?'s':'' ?> com prazo vencido — nossa equipe está trabalhando nisso.
                </div>
                <?php else: ?>
                <div class="alert alert-success py-2 mt-3 small mb-0">
                    <i class="bi bi-check-circle me-1"></i>
                    Todos os prazos de SLA estão sendo cumpridos!
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-exclamation-triangle fs-1 text-warning d-block mb-3"></i>
        <h4 class="fw-bold">Sistema em manutenção</h4>
        <p class="text-muted">Estamos trabalhando para voltar em breve. Pedimos desculpas pelo transtorno.</p>
    </div>
    <?php endif; ?>

    <div class="text-center mt-4 p-4 rounded-3" style="background:#f1f5f9">
        <h5 class="fw-bold mb-2">Precisa de suporte?</h5>
        <p class="text-muted mb-3">Abra um chamado e nossa equipe responde dentro do prazo de SLA.</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="<?= APP_URL ?>/?c=auth&a=login" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Abrir chamado
            </a>
            <a href="<?= APP_URL ?>/?c=publico&a=faq" class="btn btn-outline-secondary">
                <i class="bi bi-question-circle me-1"></i>Ver FAQ
            </a>
        </div>
    </div>
</div>
