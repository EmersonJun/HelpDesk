<?php
class NotificacaoModel extends BaseModel {
    protected string $table = 'notificacoes';

    public function criar(array $dados): int {
        return parent::criar($dados);
    }

    public function criarParaUsuario(int $idUsuario, string $msg, ?int $idChamado = null, string $tipo = 'info'): void {
        $this->criar([
            'id_usuario' => $idUsuario,
            'id_chamado' => $idChamado,
            'mensagem'   => $msg,
            'tipo'       => $tipo,
        ]);
    }

    public function listarPorUsuario(int $idUsuario, int $limite = 20): array {
        $st = $this->db->prepare("
            SELECT * FROM notificacoes
            WHERE id_usuario = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $st->execute([$idUsuario, $limite]);
        return $st->fetchAll();
    }

    public function contarNaoLidas(int $idUsuario): int {
        $st = $this->db->prepare("
            SELECT COUNT(*) AS t FROM notificacoes
            WHERE id_usuario = ? AND lida = 0
        ");
        $st->execute([$idUsuario]);
        return (int)$st->fetch()->t;
    }

    public function marcarTodasLidas(int $idUsuario): void {
        $st = $this->db->prepare("UPDATE notificacoes SET lida = 1 WHERE id_usuario = ?");
        $st->execute([$idUsuario]);
    }
}
