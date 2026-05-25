<?php
class CategoriaModel extends BaseModel {
    protected string $table = 'categorias';

    public function listarAtivas(): array {
        $st = $this->db->prepare("SELECT * FROM categorias WHERE ativo=1 ORDER BY nome");
        $st->execute();
        return $st->fetchAll();
    }

    public function listarComContagem(): array {
        $st = $this->db->prepare("
            SELECT cat.*, COUNT(c.id) AS total_chamados
            FROM categorias cat
            LEFT JOIN chamados c ON cat.id = c.id_categoria
            GROUP BY cat.id
            ORDER BY cat.nome
        ");
        $st->execute();
        return $st->fetchAll();
    }
}
