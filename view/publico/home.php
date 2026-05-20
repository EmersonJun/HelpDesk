<!-- HERO -->
<section class="hero text-white">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge mb-3 px-3 py-2" style="background:#312e81;color:#818cf8;font-size:.85rem">
                    <i class="bi bi-stars me-1"></i> Sistema Corporativo
                </span>
                <h1 class="display-4 fw-black mb-3">
                    Suporte técnico<br>
                    <span style="color:#818cf8">organizado</span> e eficiente
                </h1>
                <p class="fs-5 mb-4" style="color:#94a3b8">
                    Gerencie chamados de TI com controle total: SLA automático,
                    Kanban, histórico completo e relatórios em tempo real.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= APP_URL ?>/?c=auth&a=registrar" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-person-plus me-2"></i>Criar conta grátis
                    </a>
                    <a href="<?= APP_URL ?>/?c=auth&a=login" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                    </a>
                </div>
                <div class="mt-4 d-flex gap-4 flex-wrap" style="color:#64748b;font-size:.85rem">
                    <span><i class="bi bi-check-circle text-success me-1"></i>Sem custo</span>
                    <span><i class="bi bi-check-circle text-success me-1"></i>Sem cartão</span>
                    <span><i class="bi bi-check-circle text-success me-1"></i>Acesso imediato</span>
                </div>
            </div>
            <div class="col-lg-6">
                <!-- Mini-dashboard decorativo -->
                <div class="rounded-3 p-4" style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08)">
                    <div class="row g-3 mb-3">
                        <?php foreach([
                            ['Abertos','3','bi-envelope-open','#3b82f6'],
                            ['Em andamento','5','bi-arrow-repeat','#f59e0b'],
                            ['Resolvidos','12','bi-check-circle','#10b981'],
                            ['SLA OK','94%','bi-shield-check','#8b5cf6'],
                        ] as [$l,$v,$i,$c]): ?>
                        <div class="col-6">
                            <div class="rounded-3 p-3" style="background:rgba(255,255,255,.06)">
                                <i class="<?= $i ?> mb-1 d-block" style="color:<?= $c ?>;font-size:1.2rem"></i>
                                <div class="fw-bold fs-4" style="color:<?= $c ?>"><?= $v ?></div>
                                <div style="color:#94a3b8;font-size:.8rem"><?= $l ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="background:rgba(255,255,255,.04);border-radius:8px;padding:12px">
                        <div class="d-flex justify-content-between mb-2" style="font-size:.8rem;color:#64748b">
                            <span>SLA mensal</span><span style="color:#10b981">94%</span>
                        </div>
                        <div style="background:rgba(255,255,255,.08);height:6px;border-radius:3px">
                            <div style="background:#10b981;width:94%;height:6px;border-radius:3px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FUNCIONALIDADES -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Tudo que você precisa</h2>
            <p class="text-muted">Um sistema completo para sua equipe de suporte</p>
        </div>
        <div class="row g-4">
            <?php foreach([
                ['bi-kanban','#4f46e5','Kanban','Board visual estilo Jira com drag-and-drop para gerenciar chamados em tempo real.'],
                ['bi-bar-chart-line','#10b981','Relatórios','Gráficos com Chart.js: por status, categoria, prioridade e evolução mensal.'],
                ['bi-alarm','#f59e0b','SLA Automático','Prazos calculados por prioridade com alertas visuais quando vencidos.'],
                ['bi-chat-dots','#8b5cf6','Comentários','Notas públicas e internas. Atendentes colaboram sem o cliente ver.'],
                ['bi-paperclip','#ef4444','Anexos','Upload seguro de arquivos em chamados e comentários (até 10MB).'],
                ['bi-shield-check','#0ea5e9','Auditoria','Log completo de todas as ações: quem fez, quando e de qual IP.'],
            ] as [$ico,$cor,$titulo,$desc]): ?>
            <div class="col-md-6 col-lg-4">
                <div class="pub-card card h-100 p-4">
                    <div class="mb-3 rounded-3 d-inline-flex p-3" style="background:<?= $cor ?>15">
                        <i class="<?= $ico ?> fs-4" style="color:<?= $cor ?>"></i>
                    </div>
                    <h5 class="fw-bold mb-2"><?= $titulo ?></h5>
                    <p class="text-muted small mb-0"><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PERFIS -->
<section class="py-5 section-alt">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Três perfis de acesso</h2>
            <p class="text-muted">Cada usuário vê apenas o que precisa</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach([
                ['bg-info','text-dark','Cliente','Abre chamados, acompanha status, comenta e anexa arquivos.','bi-person'],
                ['bg-warning','text-dark','Atendente','Atende, classifica, comenta internamente e usa o Kanban.','bi-headset'],
                ['bg-danger','text-white','Admin','Gestão completa: usuários, categorias, prioridades e relatórios.','bi-shield-fill'],
            ] as [$bg,$fg,$nome,$desc,$ico]): ?>
            <div class="col-md-4">
                <div class="pub-card card text-center p-4 h-100">
                    <div class="mb-3">
                        <span class="badge <?= $bg ?> <?= $fg ?> px-3 py-2 rounded-pill fs-6">
                            <i class="<?= $ico ?> me-1"></i><?= $nome ?>
                        </span>
                    </div>
                    <p class="text-muted small mb-0"><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5" style="background:#4f46e5">
    <div class="container text-center text-white py-3">
        <h2 class="fw-bold mb-3">Pronto para começar?</h2>
        <p class="mb-4 opacity-75">Crie sua conta em menos de 1 minuto. Sem cartão de crédito.</p>
        <a href="<?= APP_URL ?>/?c=auth&a=registrar" class="btn btn-light btn-lg px-5 fw-bold">
            <i class="bi bi-rocket-takeoff me-2"></i>Criar conta grátis
        </a>
    </div>
</section>
