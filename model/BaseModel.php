<?php
abstract class BaseModel {
    protected PDO $db;
    protected string $table;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function buscarPorId(int $id): ?object {
        $st = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function criar(array $dados): int {
        $cols  = implode(',', array_keys($dados));
        $ph    = implode(',', array_fill(0, count($dados), '?'));
        $st    = $this->db->prepare("INSERT INTO {$this->table} ($cols) VALUES ($ph)");
        $st->execute(array_values($dados));
        return (int)$this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool {
        $sets = implode(',', array_map(fn($k) => "$k=?", array_keys($dados)));
        $st   = $this->db->prepare("UPDATE {$this->table} SET $sets WHERE id=?");
        return $st->execute([...array_values($dados), $id]);
    }

    public function deletar(int $id): bool {
        $st = $this->db->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $st->execute([$id]);
    }
}
