<?php $pageTitle = 'Editar Chamado #'.$chamado->id.' — '.APP_NAME;
$statusMap = ['aberto'=>'Aberto','em_andamento'=>'Em Andamento','aguardando'=>'Aguardando','resolvido'=>'Resolvido','fechado'=>'Fechado'];
?>

<div class="page-header">
    <h4><i class="bi bi-pencil me-2 text-primary"></i>Editar Chamado <span class="text-muted">#<?= $chamado->id ?></span></h4>
    <p>Atualize os dados do chamado conforme necessário.</p>
</div>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['tipo'] ?> alert-auto alert-dismissible fade show">
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
<div class="col-12 col-lg-8">
<div class="form-card">
    <form method="POST" action="<?= APP_URL ?>/?c=chamados&a=update">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
        <input type="hidden" name="id" value="<?= $chamado->id ?>">

        <div class="mb-3">
            <label class="form-label">Título <span class="text-danger">*</span></label>
            <input type="text" name="titulo" class="form-control"
                   value="<?= htmlspecialchars($chamado->titulo) ?>" required maxlength="200">
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição <span class="text-danger">*</span></label>
            <textarea name="descricao" class="form-control" rows="6" required><?= htmlspecialchars($chamado->descricao) ?></textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach($statusMap as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $chamado->status===$v?'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Atendente</label>
                <select name="id_atendente" class="form-select">
                    <option value="">Sem atendente</option>
                    <?php foreach($atendentes as $at): ?>
                    <option value="<?= $at->id ?>" <?= $chamado->id_atendente==$at->id?'selected':'' ?>>
                        <?= htmlspecialchars($at->nome) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Categoria</label>
                <select name="id_categoria" class="form-select">
                    <option value="">Sem categoria</option>
                    <?php foreach($categorias as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= $chamado->id_categoria==$cat->id?'selected':'' ?>>
                        <?= htmlspecialchars($cat->nome) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prioridade</label>
                <select name="id_prioridade" class="form-select">
                    <option value="">Sem prioridade</option>
                    <?php foreach($prioridades as $p): ?>
                    <option value="<?= $p->id ?>" <?= $chamado->id_prioridade==$p->id?'selected':'' ?>>
                        <?= htmlspecialchars($p->nome) ?> (<?= $p->sla_horas ?>h)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-between gap-2 flex-wrap">
            <a href="<?= APP_URL ?>/?c=chamados&a=show&id=<?= $chamado->id ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar ao Chamado
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>
</div>
</div>
