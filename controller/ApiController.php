<?php
class ApiController extends BaseController {

    public function chamados(): void {
        $this->sessaoRequerida();
        $u  = $this->user();
        $cm = new ChamadoModel();
        $f  = ['status'=>$_GET['status']??'','busca'=>$_GET['busca']??''];
        $dados = $cm->listarComDetalhes($f, $u->id, $u->perfil);
        $this->json(['ok'=>true,'total'=>count($dados),'data'=>$dados]);
    }

    public function estatisticas(): void {
        $this->perfilRequerido(['admin','atendente']);
        $cm = new ChamadoModel();
        $this->json([
            'ok'   => true,
            'data' => [
                'por_status'    => $cm->contarPorStatus(),
                'por_categoria' => $cm->estatisticasPorCategoria(),
                'por_mes'       => $cm->estatisticasPorMes(),
                'por_prioridade'=> $cm->chamadosPorPrioridade(),
                'sla_vencidos'  => $cm->slaVencidos(),
            ],
        ]);
    }

    public function notificacoes(): void {
        $this->sessaoRequerida();
        $u  = $this->user();
        $nm = new NotificacaoModel();
        $total = $nm->contarNaoLidas($u->id);
        $lista = $nm->listarPorUsuario($u->id, 10);
        $nm->marcarTodasLidas($u->id);
        $this->json(['ok'=>true,'nao_lidas'=>$total,'data'=>$lista]);
    }

    public function chamado(): void {
        $this->sessaoRequerida();
        $id      = (int)($_GET['id'] ?? 0);
        $cm      = new ChamadoModel();
        $chamado = $cm->buscarComDetalhes($id);
        if (!$chamado) $this->json(['ok'=>false,'msg'=>'Não encontrado'], 404);
        $this->json(['ok'=>true,'data'=>$chamado]);
    }
}
