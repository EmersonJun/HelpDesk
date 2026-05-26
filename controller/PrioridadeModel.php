<?php
class PrioridadeModel extends BaseModel {
    protected string $table = 'prioridades';

    public function listarOrdenadas(): array {
        $st = $this->db->prepare("SELECT * FROM prioridades ORDER BY nivel DESC");
        $st->execute();
        return $st->fetchAll();
    }
}
