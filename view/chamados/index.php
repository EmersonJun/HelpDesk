<?php $pageTitle = 'Chamados — '.APP_NAME;
function sLabel(string $s): string {
    return ['aberto'=>'Aberto','em_andamento'=>'Em Andamento','aguardando'=>'Aguardando',
            'resolvido'=>'Resolvido','fechado'=>'Fechado'][$s] ?? $s;
}
function sClass(string $s): string { return 's-'.$s; }
$podeGerenciar = in_array($usuarioSessao->perfil, ['atendente','admin']);
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-ticket-detailed me-2 text-primary"></i>Chamados</h4>
        <p>Total: <strong><?= count($chamados) ?></strong> chamado(s) encontrado(s)</p>
    </div>
    <a href="<?= APP_URL ?>/?c=chamados&a=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo Chamado
    </a>
</div>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['tipo'] ?> alert-dismissible alert-auto fade show">
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-3">
        <form method="GET" action="<?= APP_URL ?>/" class="row g-2 align-items-end">
            <input type="hidden" name="c" value="chamados">
            <input type="hidden" name="a" value="index">
            <div class="col-12 col-md-4">
                <input type="text" name="busca" class="form-control form-control-sm"
                       placeholder="🔍 Buscar título ou descrição…"
                       value="<?= htmlspecialchars($f['busca']) ?>">
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos status</option>
                    <?php foreach(['aberto','em_andamento','aguardando','resolvido','fechado'] as $s): ?>
                    <option value="<?= $s ?>" <?= $f['status']===$s?'selected':'' ?>><?= sLabel($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="categoria" class="form-select form-select-sm">
                    <option value="">Todas categorias</option>
                    <?php foreach($categorias as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= $f['categoria']==(string)$cat->id?'selected':'' ?>>
                        <?= htmlspecialchars($cat->nome) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="prioridade" class="form-select form-select-sm">
                    <option value="">Todas prioridades</option>
                    <?php foreach($prioridades as $p): ?>
                    <option value="<?= $p->id ?>" <?= $f['prioridade']==(string)$p->id?'selected':'' ?>>
                        <?= htmlspecialchars($p->nome) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-1">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="sla_vencido" id="slaCk" value="1"
                           <?= $f['sla_vencido']?'checked':'' ?>>
                    <label class="form-check-label small" for="slaCk">SLA</label>
                </div>
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i></button>
                <a href="<?= APP_URL ?>/?c=chamados&a=index" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Título</th><th>Solicitante</th><th>Categoria</th>
                    <th>Prioridade</th><th>Status</th><th>SLA</th><th>Abertura</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($chamados)): ?>
            <tr><td colspan="9" class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Nenhum chamado encontrado com esses filtros.
            </td></tr>
            <?php else: ?>
            <?php foreach($chamados as $c): ?>
            <tr>
                <td>
                    <a href="<?= APP_URL ?>/?c=chamados&a=show&id=<?= $c->id ?>"
                       class="fw-bold text-primary">#<?= $c->id ?></a>
                </td>
                <td>
                    <a href="<?= APP_URL ?>/?c=chamados&a=show&id=<?= $c->id ?>"
                       class="text-dark text-decoration-none fw-semibold">
                        <?= htmlspecialchars($c->titulo) ?>
                    </a>
                    <?php if ($c->total_comentarios > 0): ?>
                    <span class="badge bg-light border text-muted ms-1 small">
                        <i class="bi bi-chat-dots"></i> <?= $c->total_comentarios ?>
                    </span>
                    <?php endif; ?>
                </td>
                <td class="small text-muted"><?= htmlspecialchars($c->nome_usuario) ?></td>
                <td>
                    <?php if ($c->nome_categoria): ?>
                    <span class="badge" style="background:<?= $c->cor_categoria ?>20;color:<?= $c->cor_categoria ?>">
                        <i class="<?= $c->icone_categoria ?>"></i> <?= htmlspecialchars($c->nome_categoria) ?>
                    </span>
                    <?php else: ?><span class="text-muted small">—</span><?php endif; ?>
                </td>
                <td>
                    <?php if ($c->nome_prioridade): ?>
                    <span class="fw-semibold" style="color:<?= $c->cor_prioridade ?>">
                        <span class="priority-dot me-1" style="background:<?= $c->cor_prioridade ?>"></span>
                        <?= htmlspecialchars($c->nome_prioridade) ?>
                    </span>
                    <?php else: ?><span class="text-muted small">—</span><?php endif; ?>
                </td>
                <td><span class="badge-status <?= sClass($c->status) ?>"><?= sLabel($c->status) ?></span></td>
                <td>
                    <?php if ($c->prazo_sla): ?>
                        <?php if ($c->sla_vencido): ?>
                        <span class="sla-vencido-badge"><i class="bi bi-alarm-fill me-1"></i>Vencido</span>
                        <?php else: ?>
                        <span class="sla-ok-badge"><i class="bi bi-check-circle me-1"></i>OK</span>
                        <?php endif; ?>
                    <?php else: ?><span class="text-muted small">—</span><?php endif; ?>
                </td>
                <td class="small text-muted"><?= date('d/m/y H:i', strtotime($c->created_at)) ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="<?= APP_URL ?>/?c=chamados&a=show&id=<?= $c->id ?>"
                           class="btn btn-icon btn-sm btn-outline-primary" title="Visualizar">
                            <i class="bi bi-eye"></i>
                        </a>

                        <?php if ($podeGerenciar): ?>
                        <a href="<?= APP_URL ?>/?c=chamados&a=edit&id=<?= $c->id ?>"
                           class="btn btn-icon btn-sm btn-outline-secondary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <a href="<?= APP_URL ?>/?c=chamados&a=apagarChamado&id=<?= $c->id ?>"
                           class="btn btn-icon btn-sm btn-danger"
                           title="Apagar chamado"
                           data-confirm="Apagar o chamado #<?= $c->id ?> permanentemente? Comentários e anexos também serão removidos!">
                            <i class="bi bi-trash3-fill"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
