<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\Cliente;
use PDOException;

class ClienteController {

    private $clienteModel;
    private $logger;

    public function __construct() {
        $this->clienteModel = new Cliente();
        $this->logger = new Logger();
    }

    public function criarCliente($dados) {
        $camposObrigatorios = ['cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_assinaturas', 'cliente_deputado_id', 'cliente_deputado_nome', 'cliente_deputado_estado', 'cliente_cpf_cnpj'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'status_code' => 400, 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'status_code' => 400, 'message' => 'Email inválido.'];
        }

        try {
            $this->clienteModel->criar($dados);
            return ['status' => 'success', 'message' => 'Cliente inserido com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'status_code' => 409, 'message' => 'O e-mail já está cadastrado ou já existe uma assinatura para esse deputado.'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('client_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
                return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarCliente($cliente_id, $dados) {
        $camposObrigatorios = ['cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_assinaturas', 'cliente_deputado', 'cliente_deputado_id', 'cliente_deputado_nome'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'status_code' => 400, 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'status_code' => 422, 'message' => 'Email inválido.'];
        }

        try {
            $this->clienteModel->atualizar($cliente_id, $dados);
            return ['status' => 'success', 'status_code' => 200, 'message' => 'Cliente atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('client_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarClientes() {
        try {
            $busca = $this->clienteModel->listar();

            if (empty($busca)) {
                return ['status' => 'empty', 'status_code' => 200, 'message' => 'Nenhum cliente registrado'];
            }

            return ['status' => 'success', 'status_code' => 200, 'message' => count($busca) . ' cliente(s) encontrado(s)', 'dados' => $busca];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('client_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarCliente($coluna, $valor) {

        $colunasPermitidas = ['cliente_id', 'cliente_email', 'cliente_token'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'status_code' => 400, 'message' => 'Coluna inválida. Apenas cliente_id e cliente_email são permitidos.'];
        }

        try {
            $cliente = $this->clienteModel->buscar($coluna, $valor);
            if ($cliente) {
                return ['status' => 'success', 'status_code' => 200, 'dados' => $cliente];
            } else {
                return ['status' => 'not_found',  'status_code' => 200, 'message' => 'Cliente não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('client_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarCliente($cliente_id) {
        try {
            $result = $this->buscarCliente('cliente_id', $cliente_id);

            if ($result['status'] == 'not_found') {
                return $result;
            }

            $this->clienteModel->apagar($cliente_id);
            return ['status' => 'success', 'status_code' => 200, 'message' => 'Cliente apagado com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 409, 'message' => 'Erro: Não é possível apagar o cliente. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('client_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
