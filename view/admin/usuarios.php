<?php $pageTitle = 'Usuários — '.APP_NAME; ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-people me-2 text-primary"></i>Gestão de Usuários</h4>
        <p>Total: <strong><?= count($usuarios) ?></strong> usuário(s)</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="limparModal()">
        <i class="bi bi-person-plus me-1"></i> Novo Usuário
    </button>
</div>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['tipo'] ?> alert-auto alert-dismissible fade show">
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" action="<?= APP_URL ?>/" class="row g-2 align-items-center">
            <input type="hidden" name="c" value="admin">
            <input type="hidden" name="a" value="usuarios">
            <div class="col-12 col-md-5">
                <input type="text" name="busca" class="form-control form-control-sm"
                       placeholder="🔍 Buscar por nome ou e-mail…"
                       value="<?= htmlspecialchars($f['busca']) ?>">
            </div>
            <div class="col-6 col-md-3">
                <select name="perfil" class="form-select form-select-sm">
                    <option value="">Todos perfis</option>
                    <option value="admin"     <?= $f['perfil']==='admin'    ?'selected':'' ?>>Admin</option>
                    <option value="atendente" <?= $f['perfil']==='atendente'?'selected':'' ?>>Atendente</option>
                    <option value="cliente"   <?= $f['perfil']==='cliente'  ?'selected':'' ?>>Cliente</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i></button>
                <a href="<?= APP_URL ?>/?c=admin&a=usuarios" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Nome</th><th>E-mail</th><th>Perfil</th>
                    <th>Departamento</th><th>Status</th><th>Cadastro</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($usuarios as $usr): ?>
            <?php $ehEuMesmo = ((int)$usr->id === (int)$usuarioSessao->id); ?>
            <tr class="<?= !$usr->ativo ? 'table-secondary text-muted' : '' ?>">
                <td><?= $usr->id ?></td>
                <td class="fw-semibold">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:30px;height:30px;font-size:.75rem;flex-shrink:0;
                                    background:<?= $usr->perfil==='admin'?'#dc3545':($usr->perfil==='atendente'?'#f59e0b':'#3b82f6') ?>">
                            <?= strtoupper(substr($usr->nome,0,1)) ?>
                        </div>
                        <?= htmlspecialchars($usr->nome) ?>
                        <?php if ($ehEuMesmo): ?>
                        <span class="badge bg-secondary" style="font-size:.65rem">você</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="small"><?= htmlspecialchars($usr->email) ?></td>
                <td>
                    <span class="badge <?= $usr->perfil==='admin'?'bg-danger':($usr->perfil==='atendente'?'bg-warning text-dark':'bg-info text-dark') ?>">
                        <?= ucfirst($usr->perfil) ?>
                    </span>
                </td>
                <td class="small text-muted"><?= htmlspecialchars($usr->departamento ?? '—') ?></td>
                <td>
                    <?php if ($usr->ativo): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Ativo</span>
                    <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inativo</span>
                    <?php endif; ?>
                </td>
                <td class="small text-muted"><?= date('d/m/Y', strtotime($usr->created_at)) ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-icon btn-sm btn-outline-primary"
                                data-bs-toggle="modal" data-bs-target="#modalUsuario"
                                onclick="editarUsuario(<?= htmlspecialchars(json_encode($usr)) ?>)"
                                title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <?php if (!$ehEuMesmo): ?>
                        <a href="<?= APP_URL ?>/?c=admin&a=toggleUsuario&id=<?= $usr->id ?>"
                           class="btn btn-icon btn-sm <?= $usr->ativo ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                           title="<?= $usr->ativo ? 'Desativar' : 'Ativar' ?>"
                           data-confirm="<?= $usr->ativo ? 'Desativar este usuário?' : 'Ativar este usuário?' ?>">
                            <i class="bi bi-<?= $usr->ativo ? 'person-dash' : 'person-check' ?>"></i>
                        </a>

                        <a href="<?= APP_URL ?>/?c=admin&a=apagarUsuario&id=<?= $usr->id ?>"
                           class="btn btn-icon btn-sm btn-danger"
                           title="Apagar usuário permanentemente"
                           data-confirm="ATENÇÃO: Apagar o usuário '<?= htmlspecialchars($usr->nome) ?>'? Se ele tiver chamados vinculados será apenas desativado.">
                            <i class="bi bi-trash3-fill"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= APP_URL ?>/?c=admin&a=salvarUsuario">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                <input type="hidden" name="id" id="userId" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="uNome" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">E-mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="uEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Perfil</label>
                            <select name="perfil" id="uPerfil" class="form-select">
                                <option value="cliente">Cliente</option>
                                <option value="atendente">Atendente</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <input type="text" name="departamento" id="uDepto" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" id="uTel" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="ativo" id="uAtivo" value="1" checked>
                                <label class="form-check-label" for="uAtivo">Usuário ativo</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Senha <span id="senhaHint" class="text-muted small"></span></label>
                            <input type="password" name="senha" id="uSenha" class="form-control" placeholder="••••••••">
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
function limparModal() {
    document.getElementById('modalTitle').textContent = 'Novo Usuário';
    document.getElementById('userId').value  = '0';
    document.getElementById('uNome').value   = '';
    document.getElementById('uEmail').value  = '';
    document.getElementById('uPerfil').value = 'cliente';
    document.getElementById('uDepto').value  = '';
    document.getElementById('uTel').value    = '';
    document.getElementById('uSenha').value  = '';
    document.getElementById('uAtivo').checked = true;
    document.getElementById('senhaHint').textContent = '(obrigatório para novo usuário)';
}
function editarUsuario(u) {
    document.getElementById('modalTitle').textContent = 'Editar Usuário';
    document.getElementById('userId').value  = u.id;
    document.getElementById('uNome').value   = u.nome;
    document.getElementById('uEmail').value  = u.email;
    document.getElementById('uPerfil').value = u.perfil;
    document.getElementById('uDepto').value  = u.departamento ?? '';
    document.getElementById('uTel').value    = u.telefone ?? '';
    document.getElementById('uAtivo').checked = u.ativo == 1;
    document.getElementById('senhaHint').textContent = '(deixe em branco para não alterar)';
}
</script>
