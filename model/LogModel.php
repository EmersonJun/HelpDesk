<?php
class LogModel extends BaseModel {
    protected string $table = 'logs';

    public function registrar(int $idUsuario, string $acao, ?int $idChamado = null, ?string $detalhes = null): void {
        $this->criar([
            'id_usuario' => $idUsuario,
            'id_chamado' => $idChamado,
            'acao'       => $acao,
            'detalhes'   => $detalhes,
            'ip'         => self::resolverIp(),
        ]);
    }

    public function listarPorChamado(int $idChamado): array {
        $st = $this->db->prepare("
            SELECT l.*, u.nome AS nome_usuario
            FROM logs l
            JOIN usuarios u ON l.id_usuario = u.id
            WHERE l.id_chamado = ?
            ORDER BY l.created_at ASC
        ");
        $st->execute([$idChamado]);
        return $st->fetchAll();
    }

    public function listarRecentes(int $limite = 50): array {
        $st = $this->db->prepare("
            SELECT l.*, u.nome AS nome_usuario
            FROM logs l
            JOIN usuarios u ON l.id_usuario = u.id
            ORDER BY l.created_at DESC
            LIMIT ?
        ");
        $st->execute([$limite]);
        return $st->fetchAll();
    }

    /* ---- Converte IPv6 loopback para IPv4 legível ---- */
    private static function resolverIp(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        // ::1 é o loopback IPv6 (equivale a 127.0.0.1)
        if ($ip === '::1' || $ip === '0:0:0:0:0:0:0:1') return '127.0.0.1';
        // Mapeia 127.x.x.x para nome amigável
        if (str_starts_with($ip, '127.')) return $ip . ' (localhost)';
        return $ip;
    }
}
