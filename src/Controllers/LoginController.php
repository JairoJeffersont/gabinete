<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\Usuario;
use GabineteDigital\Middleware\Logger;
use PDOException;

class LoginController {

    private $usuarioModel;
    private $logger;
    private $config;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->logger = new Logger();
        $this->config = require './src/Configs/config.php';
    }

    public function Logar($email, $senha) {
        try {

            if ($this->config['master_user']['master_email'] == $email && $this->config['master_user']['master_pass'] == $senha) {
                session_start();
                $expiracao = 1 * 60 * 60;
                $_SESSION['expiracao'] = time() + $expiracao;
                $_SESSION['usuario_id'] = 1;
                $_SESSION['usuario_nome'] = $this->config['master_user']['master_name'];
                $_SESSION['usuario_nivel'] = 0;
                $_SESSION['usuario_foto'] = null;
                $_SESSION['usuario_cliente'] = 1;
                $_SESSION['cliente_nome'] = 'CLIENTE_SISTEMA';
                $_SESSION['cliente_deputado_id'] = 1;
                $_SESSION['cliente_deputado_nome'] = 'DEPUTADO_SISTEMA';
                $_SESSION['cliente_deputado_estado'] = 'BR';
                $_SESSION['cliente_assinaturas'] = 1;
                $_SESSION['cliente_token'] = uniqid();
                $this->logger->novoLog('login_access', ' - ' . $this->config['master_user']['master_name']);
                return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'invalid_email',  'message' => 'Email inválido.'];
            }

            $result = $this->usuarioModel->buscar('usuario_email', $email);

            if (empty($result)) {
                return ['status' => 'not_found', 'status_code' => 404, 'message' => 'Usuário não encontrado.'];
            }

            if (!$result[0]['usuario_ativo']) {
                return ['status' => 'deactivated', 'status_code' => 403, 'message' => 'Usuário desativado.'];
            }

            if (password_verify($senha, $result[0]['usuario_senha'])) {
                session_start();
                $expiracao = $this->config['app']['session_time'] * 60 * 60;
                $_SESSION['expiracao'] = time() + $expiracao;
                $_SESSION['usuario_id'] = $result[0]['usuario_id'];
                $_SESSION['usuario_nome'] = $result[0]['usuario_nome'];
                $_SESSION['usuario_nivel'] = $result[0]['usuario_nivel'];
                $_SESSION['usuario_foto'] = $result[0]['usuario_foto'];
                $_SESSION['usuario_cliente'] = $result[0]['usuario_cliente'];
                $_SESSION['cliente_nome'] = $result[0]['cliente_nome'];
                $_SESSION['cliente_deputado_id'] = $result[0]['cliente_deputado_id'];
                $_SESSION['cliente_deputado_nome'] = $result[0]['cliente_deputado_nome'];
                $_SESSION['cliente_deputado_estado'] = $result[0]['cliente_deputado_estado'];
                $_SESSION['cliente_assinaturas'] = $result[0]['cliente_assinaturas'];
                $_SESSION['cliente_token'] = $result[0]['cliente_token'];
                $this->logger->novoLog('login_access', ' - ' . $result[0]['usuario_nome']);
                return ['status' => 'success', 'status_code' => 200, 'message' => 'Usuário verificado com sucesso.'];
            } else {
                return ['status' => 'wrong_password', 'message' => 'Senha incorreta.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('login_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
