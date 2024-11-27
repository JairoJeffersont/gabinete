<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\PostagemStatus;
use GabineteDigital\Middleware\Logger;
use PDOException;

class PostagemStatusController {
    private $postagemStatusModel;
    private $logger;
    private $usuario_id;

    public function __construct() {
        $this->postagemStatusModel = new PostagemStatus();
        $this->logger = new Logger();
        $this->usuario_id = $_SESSION['usuario_id'];
    }

    public function criarPostagemStatus($dados) {
        $camposObrigatorios = ['postagem_status_nome'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $dados['postagem_status_criado_por'] = $this->usuario_id;

            $this->postagemStatusModel->criar($dados);
            return ['status' => 'success', 'message' => 'Status da postagem criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Este status da postagem já está cadastrado.'];
            } else {
                $this->logger->novoLog('postagem_status_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    public function listarPostagemStatus() {
        try {
            $status = $this->postagemStatusModel->listar();

            if (empty($status)) {
                return ['status' => 'empty', 'message' => 'Nenhum status de postagem registrado.'];
            }

            return ['status' => 'success', 'dados' => $status];
        } catch (PDOException $e) {
            $this->logger->novoLog('postagem_status_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function buscarPostagemStatus($coluna, $valor) {
        try {
            $status = $this->postagemStatusModel->buscar($coluna, $valor);

            if ($status) {
                return ['status' => 'success', 'dados' => $status];
            } else {
                return ['status' => 'not_found', 'message' => 'Status de postagem não encontrado.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('postagem_status_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarPostagemStatus($postagem_status_id, $dados) {
        $camposObrigatorios = ['postagem_status_nome'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->postagemStatusModel->atualizar($postagem_status_id, $dados);
            return ['status' => 'success', 'message' => 'Status de postagem atualizado com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('postagem_status_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarPostagemStatus($postagem_status_id) {
        try {
            $status = $this->buscarPostagemStatus('postagem_status_id', $postagem_status_id);

            if ($status['status'] === 'not_found') {
                return ['status' => 'not_found', 'message' => 'Status de postagem não encontrado.'];
            }

            $this->postagemStatusModel->apagar($postagem_status_id);
            return ['status' => 'success', 'message' => 'Status de postagem apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar este status de postagem. Existem registros dependentes.'];
            }

            $this->logger->novoLog('postagem_status_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
