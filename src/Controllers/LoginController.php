<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\Usuario;
use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\Cliente;
use PDOException;

/**
 * Classe responsável pelo gerenciamento do login de usuários.
 */
class LoginController {
    /** @var Usuario Modelo de usuário para interação com o banco de dados. */
    private $usuarioModel;

     /** @var Cliente Modelo de cliente para interação com o banco de dados. */
     private $clienteModel;

    /** @var Logger Middleware para gravação de logs. */
    private $logger;

    /** @var array Configurações do sistema, incluindo dados de usuário master. */
    private $config;

    /**
     * Construtor da classe LoginController.
     * Inicializa o modelo de usuário, logger e carrega as configurações do sistema.
     */
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->clienteModel = new Cliente();
        $this->logger = new Logger();
        $this->config = require './src/Configs/config.php';
    }

    /**
     * Realiza o login de um usuário.
     *
     * @param string $email Email do usuário.
     * @param string $senha Senha do usuário.
     *
     * @return array Retorna um array com o status da operação, código HTTP e mensagem correspondente.
     *
     * Status possíveis:
     * - `success` (200): Login realizado com sucesso.
     * - `invalid_email` (400): O email fornecido é inválido.
     * - `not_found` (404): Usuário não encontrado no banco de dados.
     * - `deactivated` (403): O usuário está desativado.
     * - `wrong_password` (401): A senha fornecida está incorreta.
     * - `error` (500): Erro interno do servidor.
     */
    public function Logar($email, $senha) {
        try {
            $result = $this->usuarioModel->buscar('usuario_email', $email);

            if ($this->config['master_user']['master_email'] == $email && $this->config['master_user']['master_pass'] == $senha) {
                session_start();
                $expiracao = 1 * 60 * 60; // 24 horas em segundos
                $_SESSION['expiracao'] = time() + $expiracao;
                $_SESSION['usuario_id'] = 10000;
                $_SESSION['usuario_nome'] = $this->config['master_user']['master_name'];
                $_SESSION['usuario_nivel'] = 0;
                $_SESSION['usuario_foto'] = null;
                $_SESSION['usuario_cliente'] = 1;
                $_SESSION['cliente_nome'] = 'CLIENTE_SISTEMA';
                $this->logger->novoLog('login_access', ' - ' . $this->config['master_user']['master_name']);
                return ['status' => 'success', 'status_code' => 200, 'message' => 'Usuário verificado com sucesso.'];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'invalid_email', 'status_code' => 400, 'message' => 'Email inválido.'];
            }

            if (empty($result)) {
                return ['status' => 'not_found', 'status_code' => 404, 'message' => 'Usuário não encontrado.'];
            }

            if (!$result[0]['usuario_ativo']) {
                return ['status' => 'deactivated', 'status_code' => 403, 'message' => 'Usuário desativado.'];
            }

            if (password_verify($senha, $result[0]['usuario_senha'])) {
                $cliente = $this->clienteModel->buscar('cliente_id', $result[0]['usuario_cliente']);
                session_start();
                $expiracao = 24 * 60 * 60; // 24 horas em segundos
                $_SESSION['expiracao'] = time() + $expiracao;
                $_SESSION['usuario_id'] = $result[0]['usuario_id'];
                $_SESSION['usuario_nome'] = $result[0]['usuario_nome'];
                $_SESSION['usuario_nivel'] = $result[0]['usuario_nivel'];
                $_SESSION['usuario_foto'] = $result[0]['usuario_foto'];
                $_SESSION['usuario_cliente'] = $result[0]['usuario_cliente'];
                $_SESSION['cliente_nome'] = empty($cliente) ? 'CLIENTE_SISTEMA' : $cliente[0]['cliente_nome'];
                $this->logger->novoLog('login_access', ' - ' . $result[0]['usuario_nome']);
                return ['status' => 'success', 'status_code' => 200, 'message' => 'Usuário verificado com sucesso.'];
            } else {
                return ['status' => 'wrong_password', 'status_code' => 401, 'message' => 'Senha incorreta.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('login_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
