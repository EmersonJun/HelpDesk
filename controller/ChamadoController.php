<?php
class ChamadoController extends BaseController {

    private ChamadoModel    $cm;
    private CategoriaModel  $catM;
    private PrioridadeModel $priM;
    private UsuarioModel    $usM;
    private ComentarioModel $comM;
    private AnexoModel      $anM;
    private LogModel        $logM;
    private NotificacaoModel $notM;

    public function __construct() {
        $this->cm   = new ChamadoModel();
        $this->catM = new CategoriaModel();
        $this->priM = new PrioridadeModel();
        $this->usM  = new UsuarioModel();
        $this->comM = new ComentarioModel();
        $this->anM  = new AnexoModel();
        $this->logM = new LogModel();
        $this->notM = new NotificacaoModel();
    }

    public function index(): void {
        $this->sessaoRequerida();
        $u = $this->user();
        $f = [
            'status'     => $_GET['status']     ?? '',
            'categoria'  => $_GET['categoria']  ?? '',
            'prioridade' => $_GET['prioridade'] ?? '',
            'atendente'  => $_GET['atendente']  ?? '',
            'busca'      => $_GET['busca']      ?? '',
            'sla_vencido'=> $_GET['sla_vencido']?? '',
        ];
        $chamados   = $this->cm->listarComDetalhes($f, $u->id, $u->perfil);
        $categorias = $this->catM->listarAtivas();
        $prioridades= $this->priM->listarOrdenadas();
        $atendentes = $this->usM->listarAtendentes();
        $flash      = $this->getFlash();
        $this->render('chamados/index', compact('chamados','categorias','prioridades','atendentes','f','flash'));
    }

    public function create(): void {
        $this->sessaoRequerida();
        $categorias  = $this->catM->listarAtivas();
        $prioridades = $this->priM->listarOrdenadas();
        $flash       = $this->getFlash();
        $csrf_token  = $this->gerarTokenCSRF();
        $this->render('chamados/create', compact('categorias','prioridades','flash'));
    }

    public function store(): void {
        $this->sessaoRequerida();
        $this->validarCSRF();
        $u = $this->user();

        $titulo    = trim($_POST['titulo']    ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $idCat     = (int)($_POST['id_categoria']  ?? 0) ?: null;
        $idPri     = (int)($_POST['id_prioridade'] ?? 0) ?: null;

        if (!$titulo || !$descricao) {
            $this->flash('danger', 'Título e descrição são obrigatórios.');
            $this->redirect(APP_URL . '/?c=chamados&a=create');
        }

        $prazo = $idPri ? $this->cm->calcularPrazoSla($idPri) : null;
        $id    = $this->cm->criar([
            'titulo'        => htmlspecialchars($titulo),
            'descricao'     => htmlspecialchars($descricao),
            'id_usuario'    => $u->id,
            'id_categoria'  => $idCat,
            'id_prioridade' => $idPri,
            'prazo_sla'     => $prazo,
            'status'        => 'aberto',
        ]);

        if (!empty($_FILES['anexo']['name'])) {
            $this->anM->upload($id, $u->id, $_FILES['anexo']);
        }

        $this->logM->registrar($u->id, 'CHAMADO_CRIADO', $id, "Chamado criado: {$titulo}");

        foreach ($this->usM->listarAtendentes() as $at) {
            if ($at->id !== $u->id)
                $this->notM->criarParaUsuario($at->id, "Novo chamado #{$id}: {$titulo}", $id, 'info');
        }

        $this->flash('success', "Chamado #{$id} aberto com sucesso!");
        $this->redirect(APP_URL . '/?c=chamados&a=show&id=' . $id);
    }

    public function show(): void {
        $this->sessaoRequerida();
        $u  = $this->user();
        $id = (int)($_GET['id'] ?? 0);

        $chamado = $this->cm->buscarComDetalhes($id);
        if (!$chamado) { include ROOT.'/view/errors/404.php'; return; }
        if ($u->perfil === 'cliente' && $chamado->id_usuario != $u->id) {
            include ROOT.'/view/errors/403.php'; return;
        }

        $internos    = in_array($u->perfil, ['atendente','admin']);
        $comentarios = $this->comM->listarPorChamado($id, $internos);
        $anexos      = $this->anM->listarPorChamado($id);
        $historico   = $this->logM->listarPorChamado($id);
        $atendentes  = $this->usM->listarAtendentes();
        $categorias  = $this->catM->listarAtivas();
        $prioridades = $this->priM->listarOrdenadas();
        $flash       = $this->getFlash();

        $csrf_token = $this->gerarTokenCSRF();
        $this->render('chamados/show',
            compact('chamado','comentarios','anexos','historico','atendentes','categorias','prioridades','flash','csrf_token'));
    }

    public function comentar(): void {
        $this->sessaoRequerida();
        $this->validarCSRF();
        $u         = $this->user();
        $idChamado = (int)($_POST['id_chamado'] ?? 0);
        $texto     = trim($_POST['texto'] ?? '');
        $interno   = (isset($_POST['interno']) && in_array($u->perfil, ['atendente','admin'])) ? 1 : 0;

        if (!$texto) {
            $this->flash('danger', 'Comentário vazio.');
            $this->redirect(APP_URL . '/?c=chamados&a=show&id=' . $idChamado . '#comentarios');
        }

        $chamado = $this->cm->buscarComDetalhes($idChamado);
        if (!$chamado) $this->redirect(APP_URL . '/');

        $this->comM->criar([
            'id_chamado' => $idChamado,
            'id_usuario' => $u->id,
            'texto'      => htmlspecialchars($texto),
            'interno'    => $interno,
        ]);

        if (!empty($_FILES['anexo']['name']))
            $this->anM->upload($idChamado, $u->id, $_FILES['anexo']);

        $this->logM->registrar($u->id, 'COMENTARIO', $idChamado, $interno ? 'Nota interna' : 'Comentário público');

        if ($u->id !== $chamado->id_usuario)
            $this->notM->criarParaUsuario($chamado->id_usuario, "Novo comentário no chamado #{$idChamado}", $idChamado);
        if ($chamado->id_atendente && $u->id !== (int)$chamado->id_atendente)
            $this->notM->criarParaUsuario($chamado->id_atendente, "Novo comentário no chamado #{$idChamado}", $idChamado);

        $this->redirect(APP_URL . '/?c=chamados&a=show&id=' . $idChamado . '#comentarios');
    }

    public function atualizarStatus(): void {
        $this->perfilRequerido(['atendente','admin']);
        $this->validarCSRF();
        $u         = $this->user();
        $idChamado = (int)($_POST['id_chamado'] ?? 0);
        $novoStatus= $_POST['status'] ?? '';
        $idAtend   = (int)($_POST['id_atendente'] ?? 0) ?: null;

        $validos = ['aberto','em_andamento','aguardando','resolvido','fechado'];
        if (!in_array($novoStatus, $validos)) {
            $this->flash('danger','Status inválido.');
            $this->redirect(APP_URL.'/?c=chamados&a=show&id='.$idChamado);
        }

        $chamado = $this->cm->buscarComDetalhes($idChamado);
        $anterior= $chamado->status;
        $upd     = ['status'=>$novoStatus,'id_atendente'=>$idAtend];
        if ($novoStatus==='resolvido' && $anterior!=='resolvido') $upd['resolvido_em']=date('Y-m-d H:i:s');

        $this->cm->atualizar($idChamado, $upd);
        $this->logM->registrar($u->id,'STATUS_ALTERADO',$idChamado,"{$anterior} → {$novoStatus}");
        $this->notM->criarParaUsuario(
            $chamado->id_usuario,
            "Chamado #{$idChamado} atualizado para: ".ucfirst(str_replace('_',' ',$novoStatus)),
            $idChamado, 'success'
        );

        $this->flash('success','Status atualizado!');
        $this->redirect(APP_URL.'/?c=chamados&a=show&id='.$idChamado);
    }

    public function edit(): void {
        $this->perfilRequerido(['atendente','admin']);
        $id      = (int)($_GET['id'] ?? 0);
        $chamado = $this->cm->buscarComDetalhes($id);
        if (!$chamado) { include ROOT.'/view/errors/404.php'; return; }
        $categorias  = $this->catM->listarAtivas();
        $prioridades = $this->priM->listarOrdenadas();
        $atendentes  = $this->usM->listarAtendentes();
        $flash       = $this->getFlash();
        $csrf_token  = $this->gerarTokenCSRF();
        $this->render('chamados/edit', compact('chamado','categorias','prioridades','atendentes','flash'));
    }

    public function update(): void {
        $this->perfilRequerido(['atendente','admin']);
        $this->validarCSRF();
        $u  = $this->user();
        $id = (int)($_POST['id'] ?? 0);

        $this->cm->atualizar($id, [
            'titulo'        => htmlspecialchars(trim($_POST['titulo']    ?? '')),
            'descricao'     => htmlspecialchars(trim($_POST['descricao'] ?? '')),
            'status'        => $_POST['status']        ?? 'aberto',
            'id_categoria'  => (int)($_POST['id_categoria']  ?? 0) ?: null,
            'id_prioridade' => (int)($_POST['id_prioridade'] ?? 0) ?: null,
            'id_atendente'  => (int)($_POST['id_atendente']  ?? 0) ?: null,
        ]);

        $this->logM->registrar($u->id,'CHAMADO_EDITADO',$id,'Chamado atualizado via formulário');
        $this->flash('success','Chamado atualizado!');
        $this->redirect(APP_URL.'/?c=chamados&a=show&id='.$id);
    }

    public function kanban(): void {
        $this->perfilRequerido(['atendente','admin']);
        $u      = $this->user();
        $kanban = $this->cm->kanban($u->id, $u->perfil);
        $this->render('chamados/kanban', compact('kanban'));
    }

    public function moverKanban(): void {
        $this->perfilRequerido(['atendente','admin']);
        $u      = $this->user();
        $id     = (int)($_POST['id']     ?? 0);
        $status = $_POST['status'] ?? '';
        $validos= ['aberto','em_andamento','aguardando','resolvido','fechado'];

        if (!in_array($status, $validos)) $this->json(['ok'=>false,'msg'=>'Status inválido'], 400);

        $chamado = $this->cm->buscarComDetalhes($id);
        if (!$chamado) $this->json(['ok'=>false,'msg'=>'Chamado não encontrado'], 404);

        $upd = ['status' => $status];
        if ($status === 'resolvido') $upd['resolvido_em'] = date('Y-m-d H:i:s');

        $this->cm->atualizar($id, $upd);
        $this->logM->registrar($u->id,'KANBAN_MOVE',$id,"{$chamado->status} → {$status}");
        $this->notM->criarParaUsuario(
            $chamado->id_usuario,
            "Chamado #{$id} movido para: ".ucfirst(str_replace('_',' ',$status)),
            $id
        );
        $this->json(['ok'=>true]);
    }

    public function notificacoes(): void {
        $this->sessaoRequerida();
        $u  = $this->user();
        $nm = new NotificacaoModel();
        $lista = $nm->listarPorUsuario($u->id, 15);
        $nm->marcarTodasLidas($u->id);
        $this->json(['ok'=>true, 'data'=>$lista]);
    }

    public function download(): void {
        $this->sessaoRequerida();
        $id   = (int)($_GET['id'] ?? 0);
        $anM  = new AnexoModel();
        $anex = $anM->buscarPorId($id);
        if (!$anex) { include ROOT.'/view/errors/404.php'; return; }

        $path = UPLOAD_PATH . $anex->nome_arquivo;
        if (!file_exists($path)) { include ROOT.'/view/errors/404.php'; return; }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$anex->nome_original.'"');
        header('Content-Length: '.filesize($path));
        readfile($path);
        exit;
    }

    public function apagarChamado(): void {
        $this->perfilRequerido(['atendente', 'admin']);
        $u  = $this->user();
        $id = (int)($_GET['id'] ?? 0);

        $chamado = $this->cm->buscarComDetalhes($id);
        if (!$chamado) {
            $this->flash('danger', 'Chamado não encontrado.');
            $this->redirect(APP_URL . '/?c=chamados&a=index');
        }

        $titulo = $chamado->titulo;
        $this->cm->deletar($id);
        $this->logM->registrar($u->id, 'CHAMADO_APAGADO', null,
            "Chamado #{$id} apagado: {$titulo}");

        $this->flash('success', "Chamado #{$id} \"{$titulo}\" removido.");
        $this->redirect(APP_URL . '/?c=chamados&a=index');
    }
}
