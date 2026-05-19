const APP_URL = document.querySelector('meta[name="app-url"]')?.content ?? '';

const btnNotif  = document.getElementById('btnNotif');
const notifList = document.getElementById('notif-list');

if (btnNotif && notifList) {
    btnNotif.addEventListener('click', async () => {
        notifList.innerHTML = '<div class="text-center p-3 text-muted"><div class="spinner-border spinner-border-sm"></div></div>';
        try {
            const r = await fetch(`${APP_URL}/?c=chamados&a=notificacoes`);
            const j = await r.json();
            if (!j.data.length) {
                notifList.innerHTML = '<div class="text-center p-3 text-muted small">Nenhuma notificação.</div>';
                return;
            }
            notifList.innerHTML = j.data.map(n => `
                <a href="${n.id_chamado ? APP_URL+'/?c=chamados&a=show&id='+n.id_chamado : '#'}"
                   class="notif-item ${n.lida==0?'nao-lida':''}">
                    <div class="n-msg">${n.mensagem}</div>
                    <div class="n-time">${formatDate(n.created_at)}</div>
                </a>`).join('');
            const badge = btnNotif.querySelector('.badge');
            if (badge) badge.remove();
        } catch { notifList.innerHTML = '<div class="text-center p-3 text-danger small">Erro ao carregar.</div>'; }
    });
}

function formatDate(str) {
    if (!str) return '';
    const d = new Date(str.replace(' ','T'));
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {hour:'2-digit',minute:'2-digit'});
}

document.querySelectorAll('.alert-auto').forEach(el => {
    setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        bsAlert.close();
    }, 5000);
});

document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
        if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
});

const fileInput = document.getElementById('anexoInput');
const fileLabel = document.getElementById('anexoLabel');
if (fileInput && fileLabel) {
    fileInput.addEventListener('change', () => {
        const f = fileInput.files[0];
        fileLabel.textContent = f ? f.name : 'Escolher arquivo';
    });
}

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
});

document.querySelectorAll('.kanban-cards').forEach(col => {
    Sortable.create(col, {
        group     : 'kanban',
        animation : 180,
        ghostClass: 'sortable-ghost',
        chosenClass:'sortable-chosen',
        onEnd(evt) {
            const card     = evt.item;
            const idChamado= card.dataset.id;
            const novoStatus = evt.to.dataset.status;
            if (evt.from === evt.to) return; 

            fetch(`${APP_URL}/?c=chamados&a=moverKanban`, {
                method : 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body   : `id=${idChamado}&status=${novoStatus}`,
            })
            .then(r => r.json())
            .then(j => {
                if (!j.ok) { alert('Erro ao mover chamado: ' + (j.msg??'')); location.reload(); }
                else {
                    document.querySelectorAll('.kanban-col-header .k-count').forEach(el => {
                        const colEl = el.closest('.kanban-col');
                        const count = colEl.querySelectorAll('.kanban-card').length;
                        el.textContent = count;
                    });
                }
            })
            .catch(() => { alert('Erro de rede.'); location.reload(); });
        }
    });
});

function criarGraficoStatus(canvasId, labels, valores, cores) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data            : valores,
                backgroundColor : cores,
                borderWidth     : 2,
                borderColor     : '#fff',
            }]
        },
        options: {
            responsive       : true,
            cutout           : '65%',
            plugins: {
                legend: { position:'bottom', labels:{ padding:16, font:{size:12} } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.raw} chamado${ctx.raw!==1?'s':''}`,
                    }
                }
            }
        }
    });
}

function criarGraficoBarras(canvasId, labels, valores, cor) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets:[{
                label          : 'Chamados',
                data           : valores,
                backgroundColor: cor ?? '#4f46e5',
                borderRadius   : 6,
                borderSkipped  : false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display:false } },
            scales: {
                y: { beginAtZero:true, ticks:{ precision:0 }, grid:{ color:'#f1f5f9' } },
                x: { grid:{ display:false } }
            }
        }
    });
}

function criarGraficoPizza(canvasId, labels, valores, cores) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    new Chart(ctx, {
        type:'pie',
        data:{
            labels,
            datasets:[{ data:valores, backgroundColor:cores, borderWidth:2, borderColor:'#fff' }]
        },
        options:{
            responsive:true,
            plugins:{ legend:{ position:'bottom', labels:{ padding:14, font:{size:12} } } }
        }
    });
}
