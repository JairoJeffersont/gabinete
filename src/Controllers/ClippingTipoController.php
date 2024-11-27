<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\ClippingTipo;
use GabineteDigital\Middleware\Logger;
use PDOException;

class ClippingTipoController {
    private $clippingTipoModel;
    private $logger;
    private $usuario_id;

    public function __construct() {
        $this->clippingTipoModel = new ClippingTipo();
        $this->logger = new Logger();
        $this->usuario_id = $_SESSION['usuario_id'];
    }

    public function criarClippingTipo($dados) {
        $camposObrigatorios = ['clipping_tipo_nome', 'clipping_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $dados['clipping_tipo_criado_por'] = $this->usuario_id;

            $this->clippingTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de clipping criado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de clipping já está cadastrado.'];
            } else {
                $this->logger->novoLog('clipping_tipo_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    public function listarClippingTipos() {
        try {
            $clippingTipos = $this->clippingTipoModel->listar();

            if (empty($clippingTipos)) {
                return ['status' => 'empty', 'message' => 'Nenhum tipo de clipping registrado.'];
            }

            return ['status' => 'success', 'dados' => $clippingTipos];
        } catch (PDOException $e) {
            $this->logger->novoLog('clipping_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function buscarClippingTipo($coluna, $valor) {
        try {
            $clippingTipo = $this->clippingTipoModel->buscar($coluna, $valor);
            if ($clippingTipo) {
                return ['status' => 'success', 'dados' => $clippingTipo];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de clipping não encontrado.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('clipping_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarClippingTipo($clipping_tipo_id, $dados) {
        $camposObrigatorios = ['clipping_tipo_nome', 'clipping_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->clippingTipoModel->atualizar($clipping_tipo_id, $dados);
            return ['status' => 'success', 'message' => 'Tipo de clipping atualizado com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('clipping_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarClippingTipo($clipping_tipo_id) {
        try {
            $result = $this->buscarClippingTipo('clipping_tipo_id', $clipping_tipo_id);

            if ($result['status'] === 'not_found') {
                return ['status' => 'not_found', 'message' => 'Tipo de clipping não encontrado.'];
            }

            $this->clippingTipoModel->apagar($clipping_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de clipping apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar o tipo de clipping. Existem registros dependentes.'];
            }

            $this->logger->novoLog('clipping_tipo_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
