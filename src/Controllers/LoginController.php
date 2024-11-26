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
        $this->config = require '../src/configs/config.php';
    }

    public function Logar($email, $senha) {
        try {
            $result = $this->usuarioModel->buscar('usuario_email', $email);

            if ($this->config['master_user']['master_email'] == $email && $this->config['master_user']['master_pass'] == $senha) {
                session_start();
                $_SESSION['usuario_id'] = 10000;
                $_SESSION['usuario_nome'] = $this->config['master_user']['master_name'];
                $_SESSION['usuario_nivel'] = 1;
                $_SESSION['usuario_foto'] = null;
                $this->logger->novoLog('login_access', ' - ' . $this->config['master_user']['master_name']);
                return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
                exit;
            }

            if (empty($result)) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado.'];
                exit;
            }

            if (!$result[0]['usuario_ativo']) {
                return ['status' => 'deactivated', 'message' => 'Usuário desativado.'];
                exit;
            }

            if (password_verify($senha, $result[0]['usuario_senha'])) {
                session_start();
                $_SESSION['usuario_id'] = $result[0]['usuario_id'];
                $_SESSION['usuario_nome'] = $result[0]['usuario_nome'];
                $_SESSION['usuario_nivel'] = $result[0]['usuario_nivel'];
                $_SESSION['usuario_foto'] = $result[0]['usuario_foto'];
                $this->logger->novoLog('login_access', ' - ' . $result[0]['usuario_nome']);
                return ['status' => 'success', 'message' => 'Usuário verificado com sucesso.'];
                exit;
            } else {
                return ['status' => 'wrong_password', 'message' => 'Senha incorreta.'];
                exit;
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('login_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor.'];
            exit;
        }
    }
}
