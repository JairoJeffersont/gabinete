<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\Cliente;
use PDOException;

/**
 * Classe ClienteController
 *
 * Gerencia operações relacionadas a clientes, como criação, atualização, listagem, busca e remoção.
 */
class ClienteController {

    /**
     * Modelo Cliente.
     *
     * @var Cliente
     */
    private $clienteModel;

    /**
     * Logger para registrar logs.
     *
     * @var Logger
     */
    private $logger;

    /**
     * Construtor da classe ClienteController.
     *
     * Inicializa o modelo de Cliente e o Logger.
     */
    public function __construct() {
        $this->clienteModel = new Cliente();
        $this->logger = new Logger();
    }

    /**
     * Cria um novo cliente no banco de dados.
     *
     * Verifica se todos os campos obrigatórios estão presentes e válidos. Caso contrário, retorna erros específicos.
     *
     * @param array $dados Associativo contendo as informações do cliente.
     *     - cliente_nome: string
     *     - cliente_email: string
     *     - cliente_telefone: string
     *     - cliente_ativo: int (0 ou 1)
     *     - cliente_assinaturas: int
     *     - cliente_deputado: string
     *     - cliente_deputado_id: int
     *     - cliente_deputado_nome: string
     *
     * @return array Retorna um array com status, código e mensagem. Exemplo:
     *     - status: 'success', 'error', 'bad_request', 'invalid_email', 'duplicated'
     *     - message: Mensagem detalhada sobre a operação.
     */
    public function criarCliente($dados) {
        $camposObrigatorios = ['cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_assinaturas', 'cliente_deputado_id', 'cliente_deputado_nome', 'cliente_deputado_estado'];

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
                return ['status' => 'duplicated', 'status_code' => 409, 'message' => 'O e-mail já está cadastrado.'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('client_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
                return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    /**
     * Atualiza as informações de um cliente existente.
     *
     * Verifica se todos os campos obrigatórios estão presentes e válidos. Caso contrário, retorna erros específicos.
     *
     * @param int $cliente_id ID do cliente a ser atualizado.
     * @param array $dados Associativo contendo as informações do cliente.
     *     - cliente_nome: string
     *     - cliente_email: string
     *     - cliente_telefone: string
     *     - cliente_ativo: int (0 ou 1)
     *     - cliente_assinaturas: int
     *     - cliente_deputado: string
     *     - cliente_deputado_id: int
     *     - cliente_deputado_nome: string
     *
     * @return array Retorna um array com status, código e mensagem. Exemplo:
     *     - status: 'success', 'error', 'bad_request', 'invalid_email'
     *     - message: Mensagem detalhada sobre a operação.
     */
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

    /**
     * Lista todos os clientes registrados no banco de dados.
     *
     * @return array Retorna um array com status, código e dados dos clientes. Exemplo:
     *     - status: 'success', 'empty', 'error'
     *     - message: Número de clientes encontrados ou mensagem de erro.
     *     - dados: Array associativo com os clientes ou vazio.
     */
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

    /**
     * Busca um cliente baseado em uma coluna e valor.
     *
     * Apenas cliente_id e cliente_email são permitidos como colunas para pesquisa.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor a ser buscado na coluna.
     *
     * @return array Retorna um array com status, código e dados do cliente ou mensagens de erro. Exemplo:
     *     - status: 'success', 'not_found', 'bad_request', 'error'
     *     - message: Mensagem detalhada sobre a operação.
     *     - dados: Array associativo com o cliente ou vazio.
     */
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

    /**
     * Apaga um cliente do banco de dados.
     *
     * Verifica se o cliente tem dependências antes de ser apagado. Retorna erro se houver dependências.
     *
     * @param int $cliente_id ID do cliente a ser apagado.
     *
     * @return array Retorna um array com status, código e mensagem. Exemplo:
     *     - status: 'success', 'error'
     *     - message: Mensagem detalhada sobre a operação.
     */
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
