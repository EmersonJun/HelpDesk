<?php
session_start();
define('ROOT', _DIR_);

require_once 'config/config.php';
require_once 'config/database.php';

require_once 'model/BaseModel.php';
require_once 'model/UsuarioModel.php';
require_once 'model/ChamadoModel.php';
require_once 'model/CategoriaModel.php';
require_once 'model/PrioridadeModel.php';
require_once 'model/ComentarioModel.php';
require_once 'model/AnexoModel.php';
require_once 'model/LogModel.php';
require_once 'model/NotificacaoModel.php';

require_once 'controller/BaseController.php';
require_once 'controller/AuthController.php';
require_once 'controller/DashboardController.php';
require_once 'controller/ChamadoController.php';
require_once 'controller/AdminController.php';
require_once 'controller/ApiController.php';
require_once 'controller/PublicoController.php';

$c = preg_replace('/[^a-z]/', '', strtolower($_GET['c'] ?? ''));
$a = preg_replace('/[^a-zA-Z]/', '', $_GET['a'] ?? 'index');

$mapa = [
    'publico'   => 'PublicoController',
    'dashboard' => 'DashboardController',
    'auth'      => 'AuthController',
    'chamados'  => 'ChamadoController',
    'admin'     => 'AdminController',
    'api'       => 'ApiController',
];

$usuarioLogado = !empty($_SESSION['usuario']);
if (!$usuarioLogado && !empty($_COOKIE['helpdesk_remember'])) {
    try {
        $dados = json_decode(base64_decode($_COOKIE['helpdesk_remember']), true);
        if ($dados && isset($dados['email'], $dados['hash'])) {
            $um = new UsuarioModel();
            $u  = $um->buscarPorEmail($dados['email']);
            if ($u) {
                $hashEsperado = hash_hmac('sha256', $u->email . $u->senha, COOKIE_SECRET);
                if (hash_equals($hashEsperado, $dados['hash'])) {
                    $_SESSION['usuario'] = (array)$u;
                    $usuarioLogado = true;
                }
            }
        }
    } catch (\Throwable $e) {
    }
}

if ($c === '') {
    if ($usuarioLogado) {
        $c = 'dashboard';
    } else {
        $c = 'publico';
        $a = 'index';
    }
}

$classe = $mapa[$c] ?? null;

if (!$classe || !method_exists($classe, $a)) {
    http_response_code(404);
    include ROOT . '/view/errors/404.php';
    exit;
}

(new $classe())->$a();
