<?php
class UsuarioModel extends BaseModel {
    protected string $table = 'usuarios';

    public function buscarPorEmail(string $email): ?object {
        $st = $this->db->prepare(
            "SELECT * FROM usuarios WHERE email = ? AND ativo = 1"
        );
        $st->execute([$email]);
        return $st->fetch() ?: null;
    }

    public function emailExiste(string $email): bool {
        $st = $this->db->prepare(
            "SELECT COUNT(*) AS total FROM usuarios WHERE email = ?"
        );
        $st->execute([$email]);
        return (int)$st->fetch()->total > 0;
    }

    public function listarAtendentes(): array {
        $st = $this->db->prepare(
            "SELECT id, nome, email FROM usuarios
             WHERE perfil IN ('atendente','admin') AND ativo = 1
             ORDER BY nome"
        );
        $st->execute();
        return $st->fetchAll();
    }

    public function listarTodos(array $f = []): array {
        $sql = "SELECT * FROM usuarios WHERE 1=1";
        $p   = [];
        if (!empty($f['perfil'])) {
            $sql .= " AND perfil = ?";
            $p[]  = $f['perfil'];
        }
        if (!empty($f['busca'])) {
            $sql .= " AND (nome LIKE ? OR email LIKE ?)";
            $p[]  = "%{$f['busca']}%";
            $p[]  = "%{$f['busca']}%";
        }
        $sql .= " ORDER BY nome";
        $st = $this->db->prepare($sql);
        $st->execute($p);
        return $st->fetchAll();
    }

    public function limparRegistrosSecundarios(int $id): void {
        $queries = [
            "DELETE FROM notificacoes WHERE id_usuario = ?",
            "DELETE FROM logs         WHERE id_usuario = ?",
            "DELETE FROM comentarios  WHERE id_usuario = ?",
            "DELETE FROM anexos       WHERE id_usuario = ?",
        ];
        foreach ($queries as $sql) {
            $st = $this->db->prepare($sql);
            $st->execute([$id]);
        }
    }

    public function buscarParaRecuperacao(string $email, string $cpf, string $nascimento): ?object {
        $st = $this->db->prepare(
            "SELECT * FROM usuarios
             WHERE email = ?
             AND cpf = ?
             AND data_nascimento = ?
             AND ativo = 1"
        );
        $st->execute([$email, $cpf, $nascimento]);
        return $st->fetch() ?: null;
    }

    public function atualizarSenha(int $id, string $novaSenha): void {
        $this->atualizar($id, [
            'senha' => password_hash($novaSenha, PASSWORD_DEFAULT)
        ]);
    }

}