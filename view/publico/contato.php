<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Fale Conosco</h1>
        <p class="text-muted">Envie sua mensagem — respondemos em até 1 dia útil</p>
    </div>

    <div class="row justify-content-center g-5">
        <div class="col-lg-7">
            <?php if ($enviado): ?>
            <div class="alert alert-success d-flex align-items-center gap-3 p-4 rounded-3">
                <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                <div>
                    <div class="fw-bold">Mensagem enviada com sucesso!</div>
                    <div class="small">Entraremos em contato em breve pelo e-mail informado.</div>
                </div>
            </div>
            <?php else: ?>

            <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm p-4 rounded-3">
                <form method="POST" action="<?= APP_URL ?>/?c=publico&a=contato">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nome <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nome" class="form-control"
                                   placeholder="Seu nome completo"
                                   value="<?= htmlspecialchars($_POST['nome'] ?? $nomeCookie) ?>"
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                E-mail <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="seu@email.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Assunto <span class="text-danger">*</span>
                            </label>
                            <select name="assunto" class="form-select" required>
                                <option value="">Selecione o assunto…</option>
                                <option value="duvida"     <?= ($_POST['assunto']??'')==='duvida'?'selected':'' ?>>Dúvida sobre o sistema</option>
                                <option value="problema"   <?= ($_POST['assunto']??'')==='problema'?'selected':'' ?>>Reportar problema</option>
                                <option value="sugestao"   <?= ($_POST['assunto']??'')==='sugestao'?'selected':'' ?>>Sugestão de melhoria</option>
                                <option value="comercial"  <?= ($_POST['assunto']??'')==='comercial'?'selected':'' ?>>Informações comerciais</option>
                                <option value="outro"      <?= ($_POST['assunto']??'')==='outro'?'selected':'' ?>>Outro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Mensagem <span class="text-danger">*</span>
                            </label>
                            <textarea name="mensagem" class="form-control" rows="5"
                                      placeholder="Descreva sua dúvida ou mensagem…"
                                      required><?= htmlspecialchars($_POST['mensagem'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-send me-2"></i>Enviar mensagem
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <h5 class="fw-bold mb-4">Outras formas de contato</h5>
            <?php foreach([
                ['bi-envelope-fill','#4f46e5','E-mail','admin@helpdesk.com'],
                ['bi-clock-fill','#10b981','Horário de Atendimento','Seg–Sex, 8h às 18h'],
                ['bi-geo-alt-fill','#f59e0b','Localização','Curitiba, PR — Brasil'],
                ['bi-headset','#8b5cf6','Suporte Técnico','Abra um chamado no sistema'],
            ] as [$ico,$cor,$label,$val]): ?>
            <div class="d-flex gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:<?= $cor ?>18">
                    <i class="<?= $ico ?>" style="color:<?= $cor ?>"></i>
                </div>
                <div>
                    <div class="fw-semibold small"><?= $label ?></div>
                    <div class="text-muted small"><?= $val ?></div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="p-3 rounded-3 mt-4" style="background:#f1f5f9">
                <div class="fw-semibold small mb-1">
                    <i class="bi bi-lightning-fill text-warning me-1"></i>
                    Resposta rápida
                </div>
                <div class="text-muted small">
                    Para suporte urgente, crie uma conta e abra um chamado
                    com prioridade <strong>Crítica</strong> — atendimento em até 4h.
                </div>
                <a href="<?= APP_URL ?>/?c=auth&a=registrar"
                   class="btn btn-sm btn-primary mt-2 w-100">
                    <i class="bi bi-plus-circle me-1"></i>Abrir chamado urgente
                </a>
            </div>
        </div>
    </div>
</div>
