<?php $pageTitle = 'Kanban — '.APP_NAME;
$colunas = [
    'aberto'       => ['label'=>'Aberto',       'color'=>'#3b82f6','bg'=>'#dbeafe'],
    'em_andamento' => ['label'=>'Em Andamento',  'color'=>'#f59e0b','bg'=>'#fef3c7'],
    'aguardando'   => ['label'=>'Aguardando',    'color'=>'#8b5cf6','bg'=>'#ede9fe'],
    'resolvido'    => ['label'=>'Resolvido',     'color'=>'#10b981','bg'=>'#d1fae5'],
    'fechado'      => ['label'=>'Fechado',       'color'=>'#6b7280','bg'=>'#f1f5f9'],
];
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-kanban me-2 text-primary"></i>Board Kanban</h4>
        <p>Arraste os cards entre as colunas para atualizar o status dos chamados.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= APP_URL ?>/?c=chamados&a=index" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i> Ver como Lista
        </a>
        <a href="<?= APP_URL ?>/?c=chamados&a=create" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Chamado
        </a>
    </div>
</div>

<div id="kanban-status" class="alert d-none mb-3" role="alert"></div>

<div class="kanban-board">
    <?php foreach ($colunas as $statusKey => $col): ?>
    <?php $cards = $kanban[$statusKey] ?? []; ?>
    <div class="kanban-col">
        <div class="kanban-col-header" style="background:<?= $col['bg'] ?>;color:<?= $col['color'] ?>">
            <span><?= $col['label'] ?></span>
            <span class="badge k-count" style="background:<?= $col['color'] ?>;color:#fff"><?= count($cards) ?></span>
        </div>

        <div class="kanban-cards" data-status="<?= $statusKey ?>">
            <?php if (empty($cards)): ?>
            <div class="text-center text-muted small py-4 kanban-empty">
                <i class="bi bi-inbox d-block mb-1"></i>Vazio
            </div>
            <?php else: ?>
            <?php foreach ($cards as $c): ?>
            <?php
                $priClass = '';
                if ($c->nome_prioridade) {
                    $priClass = 'pri-'.strtolower(str_replace('é','e',$c->nome_prioridade ?? ''));
                }
                if ($c->sla_vencido) $priClass .= ' sla-vencido';
            ?>
            <div class="kanban-card <?= $priClass ?>" data-id="<?= $c->id ?>">
                <div class="k-title">
                    <a href="<?= APP_URL ?>/?c=chamados&a=show&id=<?= $c->id ?>"
                       class="text-decoration-none text-dark" onclick="event.stopPropagation()">
                        #<?= $c->id ?> — <?= htmlspecialchars(mb_strimwidth($c->titulo, 0, 50, '…')) ?>
                    </a>
                </div>
                <div class="k-meta mt-2 d-flex flex-wrap gap-1">
                    <?php if ($c->nome_categoria): ?>
                    <span class="badge" style="background:<?= $c->cor_categoria ?>20;color:<?= $c->cor_categoria ?>;font-size:.68rem">
                        <i class="<?= $c->icone_categoria ?>"></i> <?= htmlspecialchars($c->nome_categoria) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($c->nome_prioridade): ?>
                    <span class="badge" style="background:<?= $c->cor_prioridade ?>20;color:<?= $c->cor_prioridade ?>;font-size:.68rem">
                        <i class="bi bi-flag-fill"></i> <?= htmlspecialchars($c->nome_prioridade) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($c->sla_vencido): ?>
                    <span class="badge bg-danger" style="font-size:.68rem"><i class="bi bi-alarm me-1"></i>SLA</span>
                    <?php endif; ?>
                </div>
                <div class="k-meta mt-2 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person me-1"></i><?= htmlspecialchars(explode(' ',$c->nome_usuario)[0]) ?></span>
                    <?php if ($c->total_comentarios > 0): ?>
                    <span><i class="bi bi-chat-dots me-1"></i><?= $c->total_comentarios ?></span>
                    <?php endif; ?>
                    <span class="text-muted" style="font-size:.7rem"><?= date('d/m', strtotime($c->created_at)) ?></span>
                </div>
                <?php if ($c->nome_atendente): ?>
                <div class="k-meta mt-1">
                    <i class="bi bi-headset me-1 text-success"></i>
                    <small><?= htmlspecialchars(explode(' ',$c->nome_atendente)[0]) ?></small>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
const statusDiv = document.getElementById('kanban-status');
function showKanbanMsg(msg, tipo = 'success') {
    statusDiv.className = `alert alert-${tipo} mb-3`;
    statusDiv.textContent = msg;
    statusDiv.classList.remove('d-none');
    setTimeout(() => statusDiv.classList.add('d-none'), 3000);
}

document.querySelectorAll('.kanban-cards').forEach(col => {
    const obs = new MutationObserver(() => {
        const empty = col.querySelector('.kanban-empty');
        const cards = col.querySelectorAll('.kanban-card');
        if (empty && cards.length > 0) empty.style.display = 'none';
        else if (empty) empty.style.display = '';
    });
    obs.observe(col, { childList: true });
});
</script>
