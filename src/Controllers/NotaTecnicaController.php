<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\NotaTecnica;
use GabineteDigital\Middleware\Logger;
use PDOException;

class NotaTecnicaController {
    private $notaTecnicaModel;
    private $logger;
    private $usuario_id;

    public function __construct() {
        $this->notaTecnicaModel = new NotaTecnica();
        $this->logger = new Logger();
        $this->usuario_id = $_SESSION['usuario_id'];
    }

    public function criarNotaTecnica($dados) {
        $camposObrigatorios = ['nota_proposicao', 'nota_titulo', 'nota_resumo', 'nota_texto'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $dados['nota_criada_por'] = $this->usuario_id;

            $this->notaTecnicaModel->criar($dados);
            return ['status' => 'success', 'message' => 'Nota técnica criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Já existe uma nota técnica com essa proposição.'];
            } else {
                $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor.'];
            }
        }
    }

    public function listarNotasTecnicas() {
        try {
            $notasTecnicas = $this->notaTecnicaModel->listar();

            if (empty($notasTecnicas)) {
                return ['status' => 'empty', 'message' => 'Nenhuma nota técnica registrada.'];
            }

            return ['status' => 'success', 'dados' => $notasTecnicas];
        } catch (PDOException $e) {
            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor.'];
        }
    }

    public function buscarNotaTecnica($coluna, $valor) {
        try {
            $notaTecnica = $this->notaTecnicaModel->buscar($coluna, $valor);
            if ($notaTecnica) {
                return ['status' => 'success', 'dados' => $notaTecnica];
            } else {
                return ['status' => 'not_found', 'message' => 'Nota técnica não encontrada.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor.'];
        }
    }

    public function atualizarNotaTecnica($nota_id, $dados) {
        $camposObrigatorios = ['nota_proposicao', 'nota_titulo', 'nota_resumo', 'nota_texto'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->notaTecnicaModel->atualizar($nota_id, $dados);
            return ['status' => 'success', 'message' => 'Nota técnica atualizada com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor.'];
        }
    }

    public function apagarNotaTecnica($nota_id) {
        try {
            $result = $this->buscarNotaTecnica('nota_id', $nota_id);

            if ($result['status'] === 'not_found') {
                return ['status' => 'not_found', 'message' => 'Nota técnica não encontrada.'];
            }

            $this->notaTecnicaModel->apagar($nota_id);
            return ['status' => 'success', 'message' => 'Nota técnica apagada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar a nota técnica. Existem registros dependentes.'];
            }

            $this->logger->novoLog('nota_tecnica_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor.'];
        }
    }
}
