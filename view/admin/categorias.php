<?php $pageTitle = 'Categorias — '.APP_NAME; ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-tags me-2 text-primary"></i>Categorias</h4>
        <p>Organize os chamados por categoria de atendimento.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCat" onclick="limparCat()">
        <i class="bi bi-plus me-1"></i> Nova Categoria
    </button>
</div>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['tipo'] ?> alert-auto alert-dismissible fade show">
    <?= htmlspecialchars($flash['msg']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-3">
<?php foreach($categorias as $cat): ?>
<div class="col-12 col-md-6 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;background:<?= $cat->cor ?>20">
                    <i class="<?= $cat->icone ?> fs-5" style="color:<?= $cat->cor ?>"></i>
                </div>
                <div>
                    <div class="fw-bold"><?= htmlspecialchars($cat->nome) ?></div>
                    <div class="small text-muted">
                        <span class="badge <?= $cat->ativo?'bg-success-subtle text-success':'bg-secondary-subtle text-secondary' ?>">
                            <?= $cat->ativo?'Ativa':'Inativa' ?>
                        </span>
                    </div>
                </div>
                <div class="ms-auto d-flex gap-1">
                    <button class="btn btn-icon btn-sm btn-outline-primary"
                            data-bs-toggle="modal" data-bs-target="#modalCat"
                            onclick="editarCat(<?= htmlspecialchars(json_encode($cat)) ?>)">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="<?= APP_URL ?>/?c=admin&a=deletarCategoria&id=<?= $cat->id ?>"
                       class="btn btn-icon btn-sm btn-outline-danger"
                       data-confirm="Remover esta categoria? Certifique-se que não há chamados vinculados.">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
            <?php if ($cat->descricao): ?>
            <p class="small text-muted mb-2"><?= htmlspecialchars($cat->descricao) ?></p>
            <?php endif; ?>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background:<?= $cat->cor ?>20;color:<?= $cat->cor ?>;border:1px solid <?= $cat->cor ?>40">
                    <?= $cat->total_chamados ?> chamado<?= $cat->total_chamados!=1?'s':'' ?>
                </span>
                <code class="small text-muted"><?= $cat->icone ?></code>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="modal fade" id="modalCat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= APP_URL ?>/?c=admin&a=salvarCategoria">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                <input type="hidden" name="id" id="catId" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="catTitle">Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="catNome" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" id="catDesc" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ícone Bootstrap Icons</label>
                            <input type="text" name="icone" id="catIcone" class="form-control" placeholder="bi-tag" value="bi-tag">
                            <div class="form-text"><a href="https://icons.getbootstrap.com" target="_blank">Ver ícones →</a></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cor</label>
                            <input type="color" name="cor" id="catCor" class="form-control form-control-color w-100" value="#6c757d">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ativo" id="catAtivo" value="1" checked>
                                <label class="form-check-label" for="catAtivo">Categoria ativa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function limparCat() {
    document.getElementById('catTitle').textContent = 'Nova Categoria';
    document.getElementById('catId').value    = '0';
    document.getElementById('catNome').value  = '';
    document.getElementById('catDesc').value  = '';
    document.getElementById('catIcone').value = 'bi-tag';
    document.getElementById('catCor').value   = '#6c757d';
    document.getElementById('catAtivo').checked = true;
}
function editarCat(c) {
    document.getElementById('catTitle').textContent = 'Editar Categoria';
    document.getElementById('catId').value    = c.id;
    document.getElementById('catNome').value  = c.nome;
    document.getElementById('catDesc').value  = c.descricao ?? '';
    document.getElementById('catIcone').value = c.icone;
    document.getElementById('catCor').value   = c.cor;
    document.getElementById('catAtivo').checked = c.ativo == 1;
}
</script>
