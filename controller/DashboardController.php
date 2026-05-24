<?php
class DashboardController extends BaseController {
    public function index(): void {
        $this->sessaoRequerida();
        $u = $this->user();

        $cm = new ChamadoModel();
        $nm = new NotificacaoModel();
        $lm = new LogModel();

        $contagens      = $cm->contarPorStatus($u->id, $u->perfil);
        $total          = array_sum($contagens);
        $chamadosRecentes = array_slice($cm->listarComDetalhes([], $u->id, $u->perfil), 0, 8);
        $slaVencidos    = $cm->slaVencidos();
        $porCategoria   = $cm->estatisticasPorCategoria();
        $porMes         = $cm->estatisticasPorMes(6);
        $porPrioridade  = $cm->chamadosPorPrioridade();
        $totalNaoLidas  = $nm->contarNaoLidas($u->id);
        $logsRecentes   = ($u->perfil === 'admin') ? $lm->listarRecentes(8) : [];

        $this->render('dashboard/index', compact(
            'contagens','total','chamadosRecentes','slaVencidos',
            'porCategoria','porMes','porPrioridade',
            'totalNaoLidas','logsRecentes'
        ));
    }
}
