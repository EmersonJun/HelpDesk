<?php
class AdminController extends BaseController {

    private UsuarioModel    $usM;
    private CategoriaModel  $catM;
    private PrioridadeModel $priM;
    private ChamadoModel    $cm;
    private LogModel        $logM;

    public function __construct() {
        $this->usM  = new UsuarioModel();
        $this->catM = new CategoriaModel();
        $this->priM = new PrioridadeModel();
        $this->cm   = new ChamadoModel();
        $this->logM = new LogModel();
    }

    public function usuarios(): void {
        $this->perfilRequerido('admin');
        $f       = ['perfil'=>$_GET['perfil']??'','busca'=>$_GET['busca']??''];
        $usuarios= $this->usM->listarTodos($f);
        $flash   = $this->getFlash();
        $this->render('admin/usuarios', compact('usuarios','f','flash'));
    }

    public function salvarUsuario(): void {
        $this->perfilRequerido('admin');
        $this->validarCSRF();
        $u  = $this->user();
        $id = (int)($_POST['id'] ?? 0);

        $dados = [
            'nome'        => htmlspecialchars(trim($_POST['nome']  ?? '')),
            'email'       => trim($_POST['email'] ?? ''),
            'perfil'      => $_POST['perfil'] ?? 'cliente',
            'departamento'=> htmlspecialchars(trim($_POST['departamento'] ?? '')),
            'telefone'    => trim($_POST['telefone'] ?? ''),
            'ativo'       => isset($_POST['ativo']) ? 1 : 0,
        ];

        if (!empty($_POST['senha'])) {
            $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        }

        if ($id > 0) {
            if (empty($dados['senha'])) unset($dados['senha']);
            $this->usM->atualizar($id, $dados);
            $acao = 'Usuário atualizado: '.$dados['email'];
        } else {
            if (empty($dados['senha'])) $dados['senha'] = password_hash('123456', PASSWORD_DEFAULT);
            $this->usM->criar($dados);
            $acao = 'Usuário criado: '.$dados['email'];
        }

        $this->logM->registrar($u->id, 'USUARIO_SALVO', null, $acao);
        $this->flash('success','Usuário salvo com sucesso!');
        $this->redirect(APP_URL.'/?c=admin&a=usuarios');
    }

    public function toggleUsuario(): void {
        $this->perfilRequerido('admin');
        $u    = $this->user();
        $id   = (int)($_GET['id'] ?? 0);
        $alvo = $this->usM->buscarPorId($id);
        if ($alvo && $alvo->id !== $u->id) {
            $novo = $alvo->ativo ? 0 : 1;
            $this->usM->atualizar($id, ['ativo'=>$novo]);
            $this->logM->registrar($u->id, 'USUARIO_TOGGLE', null, "Usuário #{$id} ativo={$novo}");
        }
        $this->redirect(APP_URL.'/?c=admin&a=usuarios');
    }

    public function categorias(): void {
        $this->perfilRequerido('admin');
        $categorias = $this->catM->listarComContagem();
        $flash      = $this->getFlash();
        $csrf_token = $this->gerarTokenCSRF();
        $this->render('admin/categorias', compact('categorias','flash'));
    }

    public function salvarCategoria(): void {
        $this->perfilRequerido('admin');
        $this->validarCSRF();
        $u  = $this->user();
        $id = (int)($_POST['id'] ?? 0);
        $dados = [
            'nome'     => htmlspecialchars(trim($_POST['nome']      ?? '')),
            'descricao'=> htmlspecialchars(trim($_POST['descricao'] ?? '')),
            'icone'    => $_POST['icone'] ?? 'bi-tag',
            'cor'      => $_POST['cor']   ?? '#6c757d',
            'ativo'    => isset($_POST['ativo']) ? 1 : 0,
        ];
        $id > 0 ? $this->catM->atualizar($id,$dados) : $this->catM->criar($dados);
        $this->logM->registrar($u->id,'CATEGORIA_SALVA',null,'Categoria: '.$dados['nome']);
        $this->flash('success','Categoria salva!');
        $this->redirect(APP_URL.'/?c=admin&a=categorias');
    }

    public function deletarCategoria(): void {
        $this->perfilRequerido('admin');
        $id = (int)($_GET['id'] ?? 0);
        $this->catM->deletar($id);
        $this->flash('success','Categoria removida.');
        $this->redirect(APP_URL.'/?c=admin&a=categorias');
    }

    public function prioridades(): void {
        $this->perfilRequerido('admin');
        $prioridades = $this->priM->listarOrdenadas();
        $flash       = $this->getFlash();
        $csrf_token  = $this->gerarTokenCSRF();
        $this->render('admin/prioridades', compact('prioridades','flash'));
    }

    public function salvarPrioridade(): void {
        $this->perfilRequerido('admin');
        $this->validarCSRF();
        $u  = $this->user();
        $id = (int)($_POST['id'] ?? 0);
        $dados = [
            'nome'     => htmlspecialchars(trim($_POST['nome'] ?? '')),
            'cor'      => $_POST['cor']       ?? '#6c757d',
            'sla_horas'=> (int)($_POST['sla_horas'] ?? 24),
            'nivel'    => (int)($_POST['nivel']     ?? 1),
        ];
        $id > 0 ? $this->priM->atualizar($id,$dados) : $this->priM->criar($dados);
        $this->logM->registrar($u->id,'PRIORIDADE_SALVA',null,'Prioridade: '.$dados['nome']);
        $this->flash('success','Prioridade salva!');
        $this->redirect(APP_URL.'/?c=admin&a=prioridades');
    }

    public function relatorios(): void {
        $this->perfilRequerido(['admin','atendente']);
        $contagens     = $this->cm->contarPorStatus();
        $porCategoria  = $this->cm->estatisticasPorCategoria();
        $porMes        = $this->cm->estatisticasPorMes(12);
        $porPrioridade = $this->cm->chamadosPorPrioridade();
        $slaVencidos   = $this->cm->slaVencidos();
        $logs          = $this->logM->listarRecentes(100);
        $this->render('admin/relatorios',
            compact('contagens','porCategoria','porMes','porPrioridade','slaVencidos','logs'));
    }


    public function apagarUsuario(): void {
        $this->perfilRequerido('admin');
        $u    = $this->user();
        $id   = (int)($_GET['id'] ?? 0);

        if ($id === (int)$u->id) {
            $this->flash('danger', 'Você não pode apagar sua própria conta.');
            $this->redirect(APP_URL . '/?c=admin&a=usuarios');
        }

        $alvo = $this->usM->buscarPorId($id);
        if (!$alvo) {
            $this->flash('danger', 'Usuário não encontrado.');
            $this->redirect(APP_URL . '/?c=admin&a=usuarios');
        }

        $temChamados = $this->cm->usuarioTemChamados($id);

        if ($temChamados) {
            $this->usM->atualizar($id, ['ativo' => 0]);
            $this->logM->registrar($u->id, 'USUARIO_DESATIVADO', null,
                "Usuário #{$id} desativado (possui chamados vinculados)");
            $this->flash('warning',
                'Usuário possui chamados vinculados e foi desativado. '
                . 'Remova ou reatribua os chamados dele para apagar permanentemente.');
        } else {
            $this->usM->limparRegistrosSecundarios($id);
            $this->usM->deletar($id);
            $this->flash('success', "Usuário \"{$alvo->nome}\" removido permanentemente.");
        }

        $this->redirect(APP_URL . '/?c=admin&a=usuarios');
    }

    public function deletarPrioridade(): void {
        $this->perfilRequerido('admin');
        $u  = $this->user();
        $id = (int)($_GET['id'] ?? 0);

        $pri = $this->priM->buscarPorId($id);
        if (!$pri) {
            $this->flash('danger', 'Prioridade não encontrada.');
            $this->redirect(APP_URL . '/?c=admin&a=prioridades');
        }

        $st = (new ChamadoModel())->db ?? null;
        $db = Database::getInstance()->getConnection();
        $check = $db->prepare("SELECT COUNT(*) FROM chamados WHERE id_prioridade = ?");
        $check->execute([$id]);
        $total = (int)$check->fetchColumn();

        if ($total > 0) {
            $this->flash('warning',
                "A prioridade {$pri->nome} está em uso por {$total} chamado(s) e não pode ser removida.");
        } else {
            $this->priM->deletar($id);
            $this->logM->registrar($u->id, 'PRIORIDADE_APAGADA', null,
                "Prioridade #{$id} ({$pri->nome}) removida");
            $this->flash('success', "Prioridade {$pri->nome} removida com sucesso.");
        }

        $this->redirect(APP_URL . '/?c=admin&a=prioridades');
    }

    public function apagarChamado(): void {
        $this->perfilRequerido('admin');
        $u  = $this->user();
        $id = (int)($_GET['id'] ?? 0);

        $chamado = $this->cm->buscarComDetalhes($id);
        if (!$chamado) {
            $this->flash('danger', 'Chamado não encontrado.');
            $this->redirect(APP_URL . '/?c=chamados&a=index');
        }

        $this->cm->deletar($id);
        $this->logM->registrar($u->id, 'CHAMADO_APAGADO', null,
            "Admin apagou chamado #{$id}: {$chamado->titulo}");
        $this->flash('success', "Chamado #{$id} removido com sucesso.");
        $this->redirect(APP_URL . '/?c=chamados&a=index');
    }
}
