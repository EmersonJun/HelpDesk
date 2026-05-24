<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Perguntas Frequentes</h1>
        <p class="text-muted">Dúvidas mais comuns sobre o HelpDesk Pro</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion" id="faqAccordion">
                <?php foreach($faqs as $i => $faq): ?>
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $i>0?'collapsed':'' ?> fw-semibold"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq<?= $i ?>">
                            <i class="bi bi-question-circle text-primary me-2"></i>
                            <?= htmlspecialchars($faq['p']) ?>
                        </button>
                    </h2>
                    <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i===0?'show':'' ?>"
                         data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            <?= htmlspecialchars($faq['r']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5 p-4 rounded-3" style="background:#f1f5f9">
                <i class="bi bi-chat-dots fs-2 text-primary d-block mb-2"></i>
                <h5 class="fw-bold">Não encontrou sua resposta?</h5>
                <p class="text-muted mb-3">Entre em contato ou abra um chamado diretamente.</p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="<?= APP_URL ?>/?c=publico&a=contato" class="btn btn-outline-primary">
                        <i class="bi bi-envelope me-1"></i>Falar conosco
                    </a>
                    <a href="<?= APP_URL ?>/?c=auth&a=registrar" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Abrir chamado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
