<?php
class ComentarioModel extends BaseModel {
    protected string $table = 'comentarios';

    public function listarPorChamado(int $idChamado, bool $internos = true): array {
        $sql = "SELECT com.*, u.nome AS nome_usuario, u.perfil AS perfil_usuario
                FROM comentarios com
                JOIN usuarios u ON com.id_usuario = u.id
                WHERE com.id_chamado = ?";
        $p = [$idChamado];

        if (!$internos) {
            $sql .= " AND com.interno = 0";
        }

        $sql .= " ORDER BY com.created_at ASC";
        $st = $this->db->prepare($sql);
        $st->execute($p);
        return $st->fetchAll();
    }
}
