<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Models\Postagem;
use GabineteDigital\Middleware\Logger;
use PDOException;

class PostagemController {
    private $postagemModel;
    private $logger;
    private $usuario_id;

    public function __construct() {
        $this->postagemModel = new Postagem();
        $this->logger = new Logger();
        $this->usuario_id = $_SESSION['usuario_id'];
    }

    public function criarPostagem($dados) {
        $camposObrigatorios = ['postagem_titulo', 'postagem_status', 'postagem_informacoes', 'postagem_midias', 'postagem_status'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $dados['postagem_criada_por'] = $this->usuario_id;
            $dados['postagem_pasta'] = uniqid();

            mkdir('arquivos/postagens/' . $dados['postagem_pasta'], 0775);

            $this->postagemModel->criar($dados);
            return ['status' => 'success', 'message' => 'Postagem criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Já existe uma postagem com este título.'];
            } else {
                $this->logger->novoLog('postagem_error', $e->getMessage());
                return ['status' => 'error', 'message' => 'Erro interno do servidor'];
            }
        }
    }

    public function listarPostagens() {
        try {
            $postagens = $this->postagemModel->listar();

            if (empty($postagens)) {
                return ['status' => 'empty', 'message' => 'Nenhuma postagem registrada.'];
            }

            return ['status' => 'success', 'dados' => $postagens];
        } catch (PDOException $e) {
            $this->logger->novoLog('postagem_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function buscarPostagem($coluna, $valor) {
        try {
            $postagem = $this->postagemModel->buscar($coluna, $valor);
            if ($postagem) {
                return ['status' => 'success', 'dados' => $postagem];
            } else {
                return ['status' => 'not_found', 'message' => 'Postagem não encontrada.'];
            }
        } catch (PDOException $e) {
            $this->logger->novoLog('postagem_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function atualizarPostagem($postagem_id, $dados) {
        $camposObrigatorios = ['postagem_titulo', 'postagem_status'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->postagemModel->atualizar($postagem_id, $dados);
            return ['status' => 'success', 'message' => 'Postagem atualizada com sucesso.'];
        } catch (PDOException $e) {
            $this->logger->novoLog('postagem_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }

    public function apagarPostagem($postagem_id) {
        try {
            $result = $this->buscarPostagem('postagem_id', $postagem_id);

            if ($result['status'] === 'not_found') {
                return ['status' => 'not_found', 'message' => 'Postagem não encontrada.'];
            }

            $caminho = '../public/arquivos/postagens/' . $result['dados'][0]['postagem_pasta'];

            $caminho = '../public/arquivos/postagens/' . $result['dados'][0]['postagem_pasta'];

            if (is_dir($caminho)) {
                $arquivos = glob($caminho . '/*'); 

                foreach ($arquivos as $arquivo) {
                    unlink($arquivo);
                }
                
                rmdir($caminho);
            } else {
                unlink($caminho);
            }


            $this->postagemModel->apagar($postagem_id);
            return ['status' => 'success', 'message' => 'Postagem apagada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'message' => 'Erro: Não é possível apagar a postagem. Existem registros dependentes.'];
            }

            $this->logger->novoLog('postagem_error', $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }
    }
}
