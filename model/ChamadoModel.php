<?php
class ChamadoModel extends BaseModel {
    protected string $table = 'chamados';

    public function listarComDetalhes(array $f = [], ?int $idUsuario = null, string $perfil = 'admin'): array {
        $sql = "SELECT c.*,
                    u.nome  AS nome_usuario,  u.email AS email_usuario,
                    a.nome  AS nome_atendente,
                    cat.nome AS nome_categoria, cat.cor AS cor_categoria, cat.icone AS icone_categoria,
                    p.nome  AS nome_prioridade, p.cor  AS cor_prioridade, p.sla_horas,
                    (SELECT COUNT(*) FROM comentarios WHERE id_chamado=c.id) AS total_comentarios,
                    IF(c.prazo_sla < NOW() AND c.status NOT IN ('resolvido','fechado'), 1, 0) AS sla_vencido
                FROM chamados c
                LEFT JOIN usuarios  u   ON c.id_usuario    = u.id
                LEFT JOIN usuarios  a   ON c.id_atendente  = a.id
                LEFT JOIN categorias cat ON c.id_categoria  = cat.id
                LEFT JOIN prioridades p  ON c.id_prioridade = p.id
                WHERE 1=1";
        $p = [];

        if ($perfil === 'cliente')         { $sql .= " AND c.id_usuario=?";     $p[] = $idUsuario; }
        if (!empty($f['status']))          { $sql .= " AND c.status=?";          $p[] = $f['status']; }
        if (!empty($f['categoria']))       { $sql .= " AND c.id_categoria=?";    $p[] = $f['categoria']; }
        if (!empty($f['prioridade']))      { $sql .= " AND c.id_prioridade=?";   $p[] = $f['prioridade']; }
        if (!empty($f['atendente']))       { $sql .= " AND c.id_atendente=?";    $p[] = $f['atendente']; }
        if (!empty($f['busca']))           { $sql .= " AND (c.titulo LIKE ? OR c.descricao LIKE ?)"; $b="%{$f['busca']}%"; $p[]=  $b; $p[]=$b; }
        if (!empty($f['sla_vencido']))     { $sql .= " AND c.prazo_sla < NOW() AND c.status NOT IN ('resolvido','fechado')"; }

        $sql .= " ORDER BY
                    CASE c.status WHEN 'aberto' THEN 1 WHEN 'em_andamento' THEN 2 WHEN 'aguardando' THEN 3 ELSE 4 END,
                    p.nivel DESC, c.created_at DESC";

        $st = $this->db->prepare($sql);
        $st->execute($p);
        return $st->fetchAll();
    }

    public function buscarComDetalhes(int $id): ?object {
        $st = $this->db->prepare("
            SELECT c.*,
                u.nome  AS nome_usuario,  u.email AS email_usuario,  u.perfil AS perfil_usuario,
                a.nome  AS nome_atendente, a.email AS email_atendente,
                cat.nome AS nome_categoria, cat.cor AS cor_categoria, cat.icone AS icone_categoria,
                p.nome  AS nome_prioridade, p.cor  AS cor_prioridade, p.sla_horas,
                IF(c.prazo_sla < NOW() AND c.status NOT IN ('resolvido','fechado'), 1, 0) AS sla_vencido,
                TIMESTAMPDIFF(MINUTE, c.created_at, IFNULL(c.resolvido_em, NOW())) AS tempo_resolucao_min
            FROM chamados c
            LEFT JOIN usuarios  u   ON c.id_usuario    = u.id
            LEFT JOIN usuarios  a   ON c.id_atendente  = a.id
            LEFT JOIN categorias cat ON c.id_categoria  = cat.id
            LEFT JOIN prioridades p  ON c.id_prioridade = p.id
            WHERE c.id=?");
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function contarPorStatus(?int $idUsuario = null, string $perfil = 'admin'): array {
        $sql = "SELECT status, COUNT(*) AS total FROM chamados WHERE 1=1";
        $p   = [];
        if ($perfil === 'cliente') { $sql .= " AND id_usuario=?"; $p[] = $idUsuario; }
        $sql .= " GROUP BY status";
        $st = $this->db->prepare($sql);
        $st->execute($p);
        $r = ['aberto'=>0,'em_andamento'=>0,'aguardando'=>0,'resolvido'=>0,'fechado'=>0];
        foreach ($st->fetchAll() as $row) $r[$row->status] = (int)$row->total;
        return $r;
    }

    public function estatisticasPorCategoria(): array {
        $st = $this->db->prepare("
            SELECT COALESCE(cat.nome,'Sem categoria') AS nome, COALESCE(cat.cor,'#6c757d') AS cor, COUNT(c.id) AS total
            FROM chamados c LEFT JOIN categorias cat ON c.id_categoria=cat.id
            GROUP BY cat.id, cat.nome, cat.cor ORDER BY total DESC");
        $st->execute();
        return $st->fetchAll();
    }

    public function estatisticasPorMes(int $meses = 6): array {
        $st = $this->db->prepare("
            SELECT DATE_FORMAT(created_at,'%Y-%m') AS mes,
                   DATE_FORMAT(created_at,'%b/%Y')  AS mes_label,
                   COUNT(*) AS total
            FROM chamados WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY mes, mes_label ORDER BY mes");
        $st->execute([$meses]);
        return $st->fetchAll();
    }

    public function chamadosPorPrioridade(): array {
        $st = $this->db->prepare("
            SELECT COALESCE(p.nome,'Sem prioridade') AS nome, COALESCE(p.cor,'#6c757d') AS cor, COUNT(c.id) AS total
            FROM chamados c LEFT JOIN prioridades p ON c.id_prioridade=p.id
            GROUP BY p.id, p.nome, p.cor ORDER BY p.nivel DESC");
        $st->execute();
        return $st->fetchAll();
    }

    public function slaVencidos(): int {
        $st = $this->db->prepare(
            "SELECT COUNT(*) AS t FROM chamados WHERE prazo_sla < NOW() AND status NOT IN ('resolvido','fechado')");
        $st->execute();
        return (int)$st->fetch()->t;
    }

    public function calcularPrazoSla(int $idPrioridade): ?string {
        $st = $this->db->prepare("SELECT sla_horas FROM prioridades WHERE id=?");
        $st->execute([$idPrioridade]);
        $row = $st->fetch();
        return $row ? date('Y-m-d H:i:s', strtotime("+{$row->sla_horas} hours")) : null;
    }

    public function kanban(?int $idUsuario, string $perfil): array {
        $todos = $this->listarComDetalhes([], $idUsuario, $perfil);
        $cols  = ['aberto'=>[],'em_andamento'=>[],'aguardando'=>[],'resolvido'=>[],'fechado'=>[]];
        foreach ($todos as $c) $cols[$c->status][] = $c;
        return $cols;
    }

    public function usuarioTemChamados(int $idUsuario): bool {
        $st = $this->db->prepare(
            "SELECT COUNT(*) AS total FROM chamados 
             WHERE id_usuario = ? OR id_atendente = ?"
        );
        $st->execute([$idUsuario, $idUsuario]);
        return (int)$st->fetch()->total > 0;
    }
}
