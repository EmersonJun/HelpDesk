<?php $pageTitle = 'Novo Chamado — '.APP_NAME; ?>

<div class="page-header">
    <h4><i class="bi bi-plus-circle me-2 text-primary"></i>Abrir Novo Chamado</h4>
    <p>Descreva detalhadamente o seu problema para que possamos atendê-lo mais rápido.</p>
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
    <form method="POST" action="<?= APP_URL ?>/?c=chamados&a=store" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

        <div class="mb-3">
            <label class="form-label">Título do chamado <span class="text-danger">*</span></label>
            <input type="text" name="titulo" class="form-control"
                   placeholder="Ex: Computador não está ligando"
                   value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required maxlength="200">
            <div class="form-text">Resumo claro e objetivo do problema.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição detalhada <span class="text-danger">*</span></label>
            <textarea name="descricao" class="form-control" rows="6" required
                      placeholder="Descreva o problema com o máximo de detalhes possível: quando começou, o que foi feito antes, mensagens de erro, etc."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Categoria</label>
                <select name="id_categoria" class="form-select">
                    <option value="">Selecione uma categoria…</option>
                    <?php foreach($categorias as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($_POST['id_categoria']??'')==$cat->id?'selected':'' ?>>
                        <?= htmlspecialchars($cat->nome) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prioridade</label>
                <select name="id_prioridade" class="form-select">
                    <option value="">Selecione a prioridade…</option>
                    <?php foreach($prioridades as $p): ?>
                    <option value="<?= $p->id ?>" <?= ($_POST['id_prioridade']??'')==$p->id?'selected':'' ?>>
                        <?= htmlspecialchars($p->nome) ?> (SLA: <?= $p->sla_horas ?>h)
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Defina conforme o impacto para a operação.</div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Anexar arquivo <span class="text-muted small">(opcional, máx. 10MB)</span></label>
            <input type="file" name="anexo" class="form-control" id="anexoInput"
                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip,.txt">
            <div class="form-text">Formatos: jpg, png, pdf, doc, docx, xls, xlsx, zip, txt</div>
        </div>

        <div class="d-flex gap-2 justify-content-end">
            <a href="<?= APP_URL ?>/?c=chamados&a=index" class="btn btn-outline-secondary">
                <i class="bi bi-x me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i> Abrir Chamado
            </button>
        </div>
    </form>
</div>
</div>

<div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-lightbulb me-1 text-warning"></i> Dicas para um bom chamado
        </div>
        <div class="card-body small text-muted">
            <ul class="mb-0 ps-3">
                <li class="mb-2">Informe quando o problema começou</li>
                <li class="mb-2">Descreva o que você tentou fazer</li>
                <li class="mb-2">Inclua mensagens de erro se houver</li>
                <li class="mb-2">Adicione prints ou documentos relevantes</li>
                <li class="mb-2">Escolha a prioridade correta para agilizar o atendimento</li>
            </ul>
        </div>
    </div>
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-clock me-1 text-primary"></i> Prazos de atendimento (SLA)
        </div>
        <div class="card-body small">
            <?php foreach($prioridades as $p): ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span><span class="priority-dot me-1" style="background:<?= $p->cor ?>"></span><?= htmlspecialchars($p->nome) ?></span>
                <span class="fw-semibold"><?= $p->sla_horas ?>h</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>
