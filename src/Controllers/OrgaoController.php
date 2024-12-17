<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\Orgao;
use GabineteDigital\Middleware\Logger;
use PDOException;

class OrgaoController {
    private $orgaoModel;
    private $logger;

    public function __construct() {
        $this->orgaoModel = new Orgao();
        $this->logger = new Logger();
    }

    public function criarOrgao($dados) {
        $camposObrigatorios = ['orgao_nome', 'orgao_endereco', 'orgao_municipio', 'orgao_estado', 'orgao_email', 'orgao_tipo', 'orgao_criado_por', 'orgao_cliente'];

        if (!filter_var($dados['orgao_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }


        try {
            $this->orgaoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Órgão inserido com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O órgão já está cadastrado.'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('orgao_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
                return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
            }
        }
    }

    public function listarOrgaos($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente) {
        try {
            $result = $this->orgaoModel->listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente);

            $total = (isset($result[0]['total'])) ? $result[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhum órgão encontrado.'];
            }

            return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $result];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    public function buscarOrgao($coluna, $valor) {
        try {
            $orgao = $this->orgaoModel->buscar($coluna, $valor);
            if ($orgao) {
                return ['status' => 'success', 'dados' => $orgao];
            } else {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    public function atualizarOrgao($orgao_id, $dados) {
        if (!filter_var($dados['orgao_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        try {
            $this->orgaoModel->atualizar($orgao_id, $dados);
            return ['status' => 'success', 'message' => 'Órgão atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    public function apagarOrgao($orgao_id) {
        try {
            $result = $this->buscarOrgao('orgao_id', $orgao_id);

            $this->orgaoModel->apagar($orgao_id);
            return ['status' => 'success', 'message' => 'Órgão apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar o órgão. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('orgao_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }
}
