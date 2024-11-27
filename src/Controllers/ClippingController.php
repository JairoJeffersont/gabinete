<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\Clipping;
use GabineteDigital\Middleware\Logger;
use GabineteDigital\Middleware\UploadFile;

use PDOException;

class ClippingController {
    private $clippingModel;
    private $logger;
    private $usuario_id;
    private $pasta_clipping;
    private $uploadFile;



    public function __construct() {
        $this->clippingModel = new Clipping();
        $this->logger = new Logger();
        $this->uploadFile = new UploadFile();

        $this->usuario_id = $_SESSION['usuario_id'];
        $this->pasta_clipping = 'arquivos/pasta_clipping/';
    }

    // Criar um novo clipping
    public function criarClipping($dados) {
        $camposObrigatorios = ['clipping_resumo', 'clipping_titulo', 'clipping_link', 'clipping_orgao', 'clipping_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (isset($dados['arquivo']['tmp_name']) && !empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->uploadFile->salvarArquivo($this->pasta_clipping, $dados['arquivo']);

            if ($uploadResult['status'] == 'upload_ok') {
                $dados['clipping_arquivo'] = $this->pasta_clipping . $uploadResult['filename'];
            } else {

                if ($uploadResult['status'] == 'error') {
                    return ['status' => 'error', 'message' => 'Erro ao fazer upload.'];
                }
            }
        }

        try {
            // Adiciona o ID do usuário que está criando o clipping
            $dados['clipping_criado_por'] = $this->usuario_id;

            // Chama o modelo para criar o clipping
            $this->clippingModel->criar($dados);
            return ['status' => 'success', 'message' => 'Clipping criado com sucesso.'];
        } catch (PDOException $e) {
            // Verifica se o erro é devido à duplicação de dados
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O clipping já está registrado.'];
            } else {
                // Log do erro
                $this->logger->novoLog('clipping_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    // Listar todos os clippings
    public function listarClippings() {
        try {
            // Chama o modelo para listar os clippings
            $clippings = $this->clippingModel->listar();

            if (empty($clippings)) {
                return ['status' => 'empty', 'message' => 'Nenhum clipping registrado.'];
            }

            return ['status' => 'success', 'dados' => $clippings];
        } catch (PDOException $e) {
            // Log do erro
            $this->logger->novoLog('clipping_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    // Buscar um clipping por uma coluna e valor
    public function buscarClipping($coluna, $valor) {
        try {
            // Chama o modelo para buscar o clipping
            $clipping = $this->clippingModel->buscar($coluna, $valor);
            if ($clipping) {
                return ['status' => 'success', 'dados' => $clipping];
            } else {
                return ['status' => 'not_found', 'message' => 'Clipping não encontrado.'];
            }
        } catch (PDOException $e) {
            // Log do erro
            $this->logger->novoLog('clipping_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    // Atualizar um clipping existente
    public function atualizarClipping($clipping_id, $dados) {
        $camposObrigatorios = ['clipping_resumo', 'clipping_titulo', 'clipping_link', 'clipping_orgao', 'clipping_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            // Chama o modelo para atualizar o clipping
            $this->clippingModel->atualizar($clipping_id, $dados);
            return ['status' => 'success', 'message' => 'Clipping atualizado com sucesso.'];
        } catch (PDOException $e) {
            // Log do erro
            $this->logger->novoLog('clipping_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    // Apagar um clipping
    public function apagarClipping($clipping_id) {
        try {
            // Verifica se o clipping existe antes de tentar apagar
            $result = $this->buscarClipping('clipping_id', $clipping_id);

            if ($result['status'] === 'not_found') {
                return ['status' => 'not_found', 'message' => 'Clipping não encontrado.'];
            }

            // Chama o modelo para apagar o clipping
            $this->clippingModel->apagar($clipping_id);
            return ['status' => 'success', 'message' => 'Clipping apagado com sucesso.'];
        } catch (PDOException $e) {
            // Verifica se o erro é devido à restrição de chave estrangeira (relacionamentos com outras tabelas)
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar o clipping. Existem registros dependentes.'];
            }

            // Log do erro
            $this->logger->novoLog('clipping_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
