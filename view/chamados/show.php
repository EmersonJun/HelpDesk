<?php $pageTitle = 'Chamado #'.$chamado->id.' — '.APP_NAME;
$u = $usuarioSessao;
$isStaff = in_array($u->perfil, ['atendente','admin']);

$statusMap  = ['aberto'=>'Aberto','em_andamento'=>'Em Andamento','aguardando'=>'Aguardando','resolvido'=>'Resolvido','fechado'=>'Fechado'];
$statusCss  = ['aberto'=>'s-aberto','em_andamento'=>'s-em_andamento','aguardando'=>'s-aguardando','resolvido'=>'s-resolvido','fechado'=>'s-fechado'];

function tempoLegivel(int $min): string {
    if ($min < 60)  return "{$min}min";
    if ($min < 1440) return round($min/60).'h';
    return round($min/1440).'d';
}
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= APP_URL ?>/?c=chamados&a=index">Chamados</a></li>
        <li class="breadcrumb-item active">#<?= $chamado->id ?></li>
    </ol>
</nav>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['tipo'] ?> alert-auto alert-dismissible fade show">
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($chamado->sla_vencido): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-alarm-fill fs-5"></i>
    <strong>SLA vencido!</strong> O prazo de atendimento foi excedido. Ação imediata necessária.
</div>
<?php endif; ?>

<div class="row g-3">
<div class="col-12 col-lg-8">

    <div class="form-card mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1">
                    <span class="text-muted fw-normal">#<?= $chamado->id ?></span>
                    <?= htmlspecialchars($chamado->titulo) ?>
                </h5>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge-status <?= $statusCss[$chamado->status] ?>"><?= $statusMap[$chamado->status] ?></span>
                    <?php if ($chamado->nome_categoria): ?>
                    <span class="badge" style="background:<?= $chamado->cor_categoria ?>20;color:<?= $chamado->cor_categoria ?>">
                        <i class="<?= $chamado->icone_categoria ?>"></i> <?= htmlspecialchars($chamado->nome_categoria) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($chamado->nome_prioridade): ?>
                    <span class="badge" style="background:<?= $chamado->cor_prioridade ?>20;color:<?= $chamado->cor_prioridade ?>">
                        <i class="bi bi-flag-fill"></i> <?= htmlspecialchars($chamado->nome_prioridade) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($chamado->sla_vencido): ?>
                    <span class="sla-vencido-badge"><i class="bi bi-alarm me-1"></i>SLA Vencido</span>
                    <?php elseif ($chamado->prazo_sla): ?>
                    <span class="sla-ok-badge"><i class="bi bi-check-circle me-1"></i>SLA OK</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex gap-2">
                <?php if ($isStaff): ?>
                <a href="<?= APP_URL ?>/?c=chamados&a=edit&id=<?= $chamado->id ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="<?= APP_URL ?>/?c=chamados&a=apagarChamado&id=<?= $chamado->id ?>"
                   class="btn btn-sm btn-outline-danger"
                   data-confirm="Apagar o chamado #<?= $chamado->id ?> definitivamente? Todos os comentários e anexos serão removidos!">
                    <i class="bi bi-trash3 me-1"></i> Apagar
                </a>
                <?php endif; ?>
                
                <a href="<?= APP_URL ?>/?c=chamados&a=index" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="bg-light rounded p-3 mb-3" style="white-space:pre-wrap;font-size:.9rem;line-height:1.6">
            <?= nl2br(htmlspecialchars($chamado->descricao)) ?>
        </div>

        <div class="row g-2 small text-muted">
            <div class="col-6 col-md-3">
                <i class="bi bi-person me-1"></i>
                <strong>Solicitante:</strong><br>
                <?= htmlspecialchars($chamado->nome_usuario) ?>
            </div>
            <div class="col-6 col-md-3">
                <i class="bi bi-headset me-1"></i>
                <strong>Atendente:</strong><br>
                <?= $chamado->nome_atendente ? htmlspecialchars($chamado->nome_atendente) : '<em>Não atribuído</em>' ?>
            </div>
            <div class="col-6 col-md-3">
                <i class="bi bi-calendar-plus me-1"></i>
                <strong>Aberto em:</strong><br>
                <?= date('d/m/Y H:i', strtotime($chamado->created_at)) ?>
            </div>
            <div class="col-6 col-md-3">
                <i class="bi bi-clock-history me-1"></i>
                <strong>Tempo total:</strong><br>
                <?= tempoLegivel((int)$chamado->tempo_resolucao_min) ?>
            </div>
            <?php if ($chamado->prazo_sla): ?>
            <div class="col-6 col-md-3">
                <i class="bi bi-alarm me-1"></i>
                <strong>Prazo SLA:</strong><br>
                <?= date('d/m/Y H:i', strtotime($chamado->prazo_sla)) ?>
            </div>
            <?php endif; ?>
            <?php if ($chamado->resolvido_em): ?>
            <div class="col-6 col-md-3">
                <i class="bi bi-check-circle me-1"></i>
                <strong>Resolvido em:</strong><br>
                <?= date('d/m/Y H:i', strtotime($chamado->resolvido_em)) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($anexos)): ?>
    <div class="form-card mb-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-paperclip me-1 text-primary"></i>Anexos (<?= count($anexos) ?>)</h6>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach($anexos as $anx): ?>
            <?php
                $ext   = strtolower(pathinfo($anx->nome_original, PATHINFO_EXTENSION));
                $icon  = in_array($ext,['jpg','jpeg','png','gif']) ? 'bi-image' :
                        ($ext==='pdf' ? 'bi-file-pdf' : 'bi-file-earmark');
                $tamanho = $anx->tamanho ? round($anx->tamanho/1024).'KB' : '';
            ?>
            <a href="<?= APP_URL ?>/?c=chamados&a=download&id=<?= $anx->id ?>"
               class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" target="_blank">
                <i class="<?= $icon ?> text-primary"></i>
                <span><?= htmlspecialchars($anx->nome_original) ?></span>
                <?php if ($tamanho): ?><small class="text-muted">(<?= $tamanho ?>)</small><?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-card mb-3" id="comentarios">
        <h6 class="fw-bold mb-3"><i class="bi bi-chat-dots me-1 text-primary"></i>Comentários (<?= count($comentarios) ?>)</h6>

        <?php if (empty($comentarios)): ?>
        <p class="text-muted small text-center py-3"><i class="bi bi-chat d-block fs-3 mb-2"></i>Nenhum comentário ainda.</p>
        <?php else: ?>
        <?php foreach($comentarios as $com): ?>
        <?php $isOwn = ($com->id_usuario == $u->id); ?>
        <div class="d-flex gap-2 mb-3 <?= $isOwn ? 'flex-row-reverse' : '' ?>">
            <div class="flex-shrink-0">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                     style="width:36px;height:36px;background:<?= $isOwn?'#4f46e5':'#64748b' ?>;font-size:.85rem">
                    <?= strtoupper(substr($com->nome_usuario,0,1)) ?>
                </div>
            </div>
            <div class="flex-grow-1">
                <?php if ($com->interno): ?>
                <div class="alert alert-warning py-2 px-3 mb-1 rounded-3" style="max-width:85%<?= $isOwn?';margin-left:auto':'' ?>">
                    <div class="d-flex justify-content-between mb-1">
                        <strong class="small"><?= htmlspecialchars($com->nome_usuario) ?></strong>
                        <span class="badge bg-warning text-dark ms-2">Nota interna</span>
                    </div>
                    <div style="white-space:pre-wrap;font-size:.88rem"><?= nl2br(htmlspecialchars($com->texto)) ?></div>
                    <div class="text-muted mt-1" style="font-size:.72rem"><?= date('d/m/Y H:i', strtotime($com->created_at)) ?></div>
                </div>
                <?php else: ?>
                <div class="rounded-3 p-3 mb-1" style="max-width:85%;background:<?= $isOwn?'#eff6ff':'#f8fafc' ?>;<?= $isOwn?'margin-left:auto':'' ?>">
                    <div class="d-flex justify-content-between mb-1">
                        <strong class="small"><?= htmlspecialchars($com->nome_usuario) ?></strong>
                        <span class="badge bg-light text-muted border ms-2"><?= ucfirst($com->perfil_usuario) ?></span>
                    </div>
                    <div style="white-space:pre-wrap;font-size:.88rem"><?= nl2br(htmlspecialchars($com->texto)) ?></div>
                    <div class="text-muted mt-1" style="font-size:.72rem"><?= date('d/m/Y H:i', strtotime($com->created_at)) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!in_array($chamado->status, ['fechado'])): ?>
        <hr class="my-3">
        <form method="POST" action="<?= APP_URL ?>/?c=chamados&a=comentar" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
            <input type="hidden" name="id_chamado" value="<?= $chamado->id ?>">
            <div class="mb-2">
                <textarea name="texto" class="form-control" rows="3"
                          placeholder="Escreva um comentário…" required></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <label class="btn btn-sm btn-outline-secondary" for="anexoComentario">
                            <i class="bi bi-paperclip me-1"></i><span id="anexoLabel">Anexar</span>
                        </label>
                        <input type="file" name="anexo" id="anexoComentario" id="anexoInput" class="d-none">
                    </div>
                    <?php if ($isStaff): ?>
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="interno" id="chkInterno" value="1">
                        <label class="form-check-label small text-warning fw-semibold" for="chkInterno">
                            <i class="bi bi-lock me-1"></i>Nota interna
                        </label>
                    </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-send me-1"></i>Enviar
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <div class="form-card">
        <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-1 text-primary"></i>Histórico de Alterações</h6>
        <?php if (empty($historico)): ?>
        <p class="text-muted small">Sem registros.</p>
        <?php else: ?>
        <div class="timeline">
            <?php foreach($historico as $log): ?>
            <div class="timeline-item">
                <div class="t-body">
                    <div class="d-flex justify-content-between">
                        <strong class="small"><?= htmlspecialchars($log->nome_usuario) ?></strong>
                        <code class="small text-muted"><?= htmlspecialchars($log->acao) ?></code>
                    </div>
                    <?php if ($log->detalhes): ?>
                    <div class="text-muted small mt-1"><?= htmlspecialchars($log->detalhes) ?></div>
                    <?php endif; ?>
                    <div class="t-time"><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?> — IP: <?= htmlspecialchars($log->ip ?? '') ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<div class="col-12 col-lg-4">

    <?php if ($isStaff && $chamado->status !== 'fechado'): ?>
    <div class="form-card mb-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-sliders me-1 text-primary"></i>Atualizar Chamado</h6>
        <form method="POST" action="<?= APP_URL ?>/?c=chamados&a=atualizarStatus">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
            <input type="hidden" name="id_chamado" value="<?= $chamado->id ?>">
            <div class="mb-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <?php foreach($statusMap as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $chamado->status===$val?'selected':'' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Atendente</label>
                <select name="id_atendente" class="form-select form-select-sm">
                    <option value="">Sem atendente</option>
                    <?php foreach($atendentes as $at): ?>
                    <option value="<?= $at->id ?>" <?= $chamado->id_atendente==$at->id?'selected':'' ?>>
                        <?= htmlspecialchars($at->nome) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-check-circle me-1"></i>Salvar Alterações
            </button>
        </form>
    </div>
    <?php endif; ?>

    <div class="form-card mb-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1 text-primary"></i>Informações</h6>
        <table class="table table-sm table-borderless mb-0 small">
            <tr><td class="text-muted fw-semibold">ID</td><td class="text-end">#<?= $chamado->id ?></td></tr>
            <tr><td class="text-muted fw-semibold">Status</td>
                <td class="text-end"><span class="badge-status <?= $statusCss[$chamado->status] ?>"><?= $statusMap[$chamado->status] ?></span></td></tr>
            <tr><td class="text-muted fw-semibold">Prioridade</td>
                <td class="text-end">
                    <?php if ($chamado->nome_prioridade): ?>
                    <span style="color:<?= $chamado->cor_prioridade ?>" class="fw-semibold"><?= htmlspecialchars($chamado->nome_prioridade) ?></span>
                    <?php else: ?>—<?php endif; ?>
                </td></tr>
            <tr><td class="text-muted fw-semibold">Categoria</td>
                <td class="text-end"><?= $chamado->nome_categoria ? htmlspecialchars($chamado->nome_categoria) : '—' ?></td></tr>
            <tr><td class="text-muted fw-semibold">SLA (horas)</td>
                <td class="text-end"><?= $chamado->sla_horas ? $chamado->sla_horas.'h' : '—' ?></td></tr>
            <tr><td class="text-muted fw-semibold">Prazo</td>
                <td class="text-end"><?= $chamado->prazo_sla ? date('d/m H:i', strtotime($chamado->prazo_sla)) : '—' ?></td></tr>
            <tr><td class="text-muted fw-semibold">Abertura</td>
                <td class="text-end"><?= date('d/m/Y H:i', strtotime($chamado->created_at)) ?></td></tr>
            <?php if ($chamado->resolvido_em): ?>
            <tr><td class="text-muted fw-semibold">Resolução</td>
                <td class="text-end"><?= date('d/m/Y H:i', strtotime($chamado->resolvido_em)) ?></td></tr>
            <?php endif; ?>
            <tr><td class="text-muted fw-semibold">Tempo decorrido</td>
                <td class="text-end"><?= tempoLegivel((int)$chamado->tempo_resolucao_min) ?></td></tr>
        </table>
    </div>

    <div class="form-card mb-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-person-circle me-1 text-primary"></i>Solicitante</h6>
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:40px;height:40px;background:#4f46e5;flex-shrink:0">
                <?= strtoupper(substr($chamado->nome_usuario,0,1)) ?>
            </div>
            <div>
                <div class="fw-semibold small"><?= htmlspecialchars($chamado->nome_usuario) ?></div>
                <div class="text-muted" style="font-size:.78rem"><?= htmlspecialchars($chamado->email_usuario) ?></div>
            </div>
        </div>
    </div>

    <?php if ($chamado->nome_atendente): ?>
    <div class="form-card">
        <h6 class="fw-bold mb-3"><i class="bi bi-headset me-1 text-primary"></i>Atendente</h6>
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:40px;height:40px;background:#10b981;flex-shrink:0">
                <?= strtoupper(substr($chamado->nome_atendente,0,1)) ?>
            </div>
            <div>
                <div class="fw-semibold small"><?= htmlspecialchars($chamado->nome_atendente) ?></div>
                <div class="text-muted" style="font-size:.78rem"><?= htmlspecialchars($chamado->email_atendente ?? '') ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
</div>

<script>
const inp = document.getElementById('anexoComentario');
const lbl = document.getElementById('anexoLabel');
if (inp && lbl) inp.addEventListener('change', () => {
    lbl.textContent = inp.files[0] ? inp.files[0].name : 'Anexar';
});
</script>
