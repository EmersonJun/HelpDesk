<?php
class AnexoModel extends BaseModel {
    protected string $table = 'anexos';

    public function listarPorChamado(int $idChamado): array {
        $st = $this->db->prepare("
            SELECT a.*, u.nome AS nome_usuario
            FROM anexos a
            JOIN usuarios u ON a.id_usuario = u.id
            WHERE a.id_chamado = ?
            ORDER BY a.created_at DESC
        ");
        $st->execute([$idChamado]);
        return $st->fetchAll();
    }

    public function upload(int $idChamado, int $idUsuario, array $file): ?int {
        if ($file['error'] !== UPLOAD_ERR_OK)    return null;
        if ($file['size']  > MAX_FILE_SIZE)       return null;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS))  return null;

        if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);

        $novo = uniqid('anexo_') . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $novo)) return null;

        return $this->criar([
            'id_chamado'    => $idChamado,
            'id_usuario'    => $idUsuario,
            'nome_original' => htmlspecialchars($file['name']),
            'nome_arquivo'  => $novo,
            'tipo'          => $file['type'],
            'tamanho'       => $file['size'],
        ]);
    }
}
