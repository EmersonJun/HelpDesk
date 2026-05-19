<?php $pageTitle = 'Prioridades — '.APP_NAME; ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-exclamation-triangle me-2 text-primary"></i>Prioridades & SLA</h4>
        <p>Configure os níveis de prioridade e os prazos de atendimento (SLA).</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPri" onclick="limparPri()">
        <i class="bi bi-plus me-1"></i> Nova Prioridade
    </button>
</div>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['tipo'] ?> alert-auto alert-dismissible fade show">
    <?= htmlspecialchars($flash['msg']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-3 mb-4">
<?php foreach($prioridades as $p): ?>
<div class="col-12 col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm h-100" style="border-top:4px solid <?= $p->cor ?> !important">
        <div class="card-body text-center py-4">
            <div class="mb-2">
                <i class="bi bi-flag-fill fs-2" style="color:<?= $p->cor ?>"></i>
            </div>
            <h5 class="fw-bold mb-1"><?= htmlspecialchars($p->nome) ?></h5>
            <div class="display-6 fw-black my-2" style="color:<?= $p->cor ?>"><?= $p->sla_horas ?>h</div>
            <div class="text-muted small mb-3">prazo de atendimento</div>
            <div class="badge mb-3" style="background:<?= $p->cor ?>20;color:<?= $p->cor ?>;border:1px solid <?= $p->cor ?>40">
                Nível <?= $p->nivel ?>
            </div>
            <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-sm btn-outline-primary"
                        data-bs-toggle="modal" data-bs-target="#modalPri"
                        onclick="editarPri(<?= htmlspecialchars(json_encode($p)) ?>)">
                    <i class="bi bi-pencil me-1"></i>Editar
                </button>
                <a href="<?= APP_URL ?>/?c=admin&a=deletarPrioridade&id=<?= $p->id ?>"
                   class="btn btn-sm btn-danger"
                   data-confirm="Apagar a prioridade '<?= htmlspecialchars($p->nome) ?>'? Só é possível se não houver chamados usando ela.">
                    <i class="bi bi-trash3-fill"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="card table-card">
    <div class="card-header bg-white py-3 fw-semibold">
        <i class="bi bi-info-circle me-1 text-primary"></i> Tabela de SLA
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Prioridade</th><th>SLA</th><th>Nível</th><th>Uso Recomendado</th></tr>
            </thead>
            <tbody>
            <?php foreach($prioridades as $p): ?>
            <tr>
                <td>
                    <span class="priority-dot me-2" style="background:<?= $p->cor ?>"></span>
                    <strong><?= htmlspecialchars($p->nome) ?></strong>
                </td>
                <td class="fw-bold" style="color:<?= $p->cor ?>"><?= $p->sla_horas ?> horas</td>
                <td><?= $p->nivel ?></td>
                <td class="small text-muted">
                    <?php $dicas = [
                        4 => 'Sistema crítico fora do ar, impacta toda a operação.',
                        3 => 'Problema grave afetando equipe ou processo importante.',
                        2 => 'Problema moderado com solução de contorno disponível.',
                        1 => 'Solicitação de baixo impacto, melhoria ou dúvida.',
                    ]; echo $dicas[$p->nivel] ?? '—'; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalPri" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= APP_URL ?>/?c=admin&a=salvarPrioridade">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                <input type="hidden" name="id" id="priId" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="priTitle">Nova Prioridade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="priNome" class="form-control" required placeholder="Ex: Alta">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cor</label>
                            <input type="color" name="cor" id="priCor" class="form-control form-control-color w-100" value="#fd7e14">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nível (1=baixo, 4=crítico)</label>
                            <input type="number" name="nivel" id="priNivel" class="form-control" min="1" max="4" value="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label">SLA em horas <span class="text-danger">*</span></label>
                            <input type="number" name="sla_horas" id="priSla" class="form-control" min="1" required value="24">
                            <div class="form-text">Tempo máximo para primeiro atendimento.</div>
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
function limparPri() {
    document.getElementById('priTitle').textContent = 'Nova Prioridade';
    document.getElementById('priId').value    = '0';
    document.getElementById('priNome').value  = '';
    document.getElementById('priCor').value   = '#fd7e14';
    document.getElementById('priNivel').value = '1';
    document.getElementById('priSla').value   = '24';
}
function editarPri(p) {
    document.getElementById('priTitle').textContent = 'Editar Prioridade';
    document.getElementById('priId').value    = p.id;
    document.getElementById('priNome').value  = p.nome;
    document.getElementById('priCor').value   = p.cor;
    document.getElementById('priNivel').value = p.nivel;
    document.getElementById('priSla').value   = p.sla_horas;
}
</script>
