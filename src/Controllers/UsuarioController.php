<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Models\Usuario;
use GabineteDigital\Middleware\UploadFile;
use GabineteDigital\Models\Cliente;
use PDOException;

/**
 * Classe UsuarioController
 *
 * Controla as operações relacionadas a usuários, incluindo criação, upload de fotos, validação de campos obrigatórios e gerenciamento de erros.
 */
class UsuarioController {

    private $usuarioModel;
    private $clienteModel;
    private $uploadFile;
    private $pasta_foto;
    private $logger;

    /**
     * Construtor da classe UsuarioController.
     *
     * Inicializa os modelos e middleware necessários para gerenciar operações relacionadas a usuários.
     */
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->clienteModel = new Cliente();
        $this->uploadFile = new UploadFile();
        $this->pasta_foto = 'public/arquivos/fotos_usuarios/';
        $this->logger = new Logger();
    }

    /**
     * Cria um novo usuário.
     *
     * Verifica se os campos obrigatórios estão presentes, valida o e-mail e faz upload de foto, se necessário.
     *
     * @param array $dados Associativo contendo as informações do usuário.
     *     - usuario_nome: string
     *     - usuario_email: string (precisa ser um e-mail válido)
     *     - usuario_telefone: string
     *     - usuario_senha: string (senha precisa ser criptografada com password_hash)
     *     - usuario_nivel: int
     *     - usuario_ativo: int (0 ou 1)
     *     - usuario_aniversario: string (data no formato 'YYYY-MM-DD')
     *     - usuario_cliente: int
     *     - usuario_foto: array (opcional) contendo 'tmp_name' e 'name' para o arquivo da foto
     *
     * @return array Retorna o status da operação, código HTTP e mensagem.
     *     - status: string ('success', 'invalid_email', 'bad_request', 'error', 'duplicated')
     *     - status_code: int (código HTTP relacionado)
     *     - message: string (mensagem descritiva da operação)
     *     - error_id: string (somente em caso de erro interno)
     */
    public function criarUsuario($dados) {
        $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_senha', 'usuario_nivel', 'usuario_ativo', 'usuario_aniversario', 'usuario_cliente'];

        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'status_code' => 400, 'message' => 'Email inválido.'];
        }

        $usuariosCount = count($this->usuarioModel->buscar('usuario_cliente', $dados['usuario_cliente']));
        $clienteAssinatura = isset($this->clienteModel->buscar('cliente_id',  $dados['usuario_cliente'])[0]['cliente_assinaturas']) ? $this->clienteModel->buscar('cliente_id',  $dados['usuario_cliente'])[0]['cliente_assinaturas'] : 1;

        if ($usuariosCount >= $clienteAssinatura) {
            return ['status' => 'forbidden', 'status_code' => 401, 'message' => "Não existem mais assinaturas disponíveis."];
        }


        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'status_code' => 400, 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (isset($dados['foto']['tmp_name']) && !empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->uploadFile->salvarArquivo($this->pasta_foto, $dados['foto']);

            if ($uploadResult['status'] == 'upload_ok') {
                $dados['usuario_foto'] = $this->pasta_foto . $uploadResult['filename'];
            } else {

                if ($uploadResult['status'] == 'error') {
                    return ['status' => 'error', 'message' => 'Erro ao fazer upload.'];
                }
            }
        }

        try {
            $this->usuarioModel->criar($dados);
            return ['status' => 'success', 'message' => 'Usuário inserido com sucesso.'];
        } catch (PDOException $e) {

            if (isset($dados['usuario_foto']) && !empty($dados['usuario_foto'])) {
                unlink($dados['usuario_foto']);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'status_code' => 409, 'message' => 'O e-mail já está cadastrado.'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('user_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
                return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
            }
        }
    }

    /**
     * Atualiza as informações de um usuário existente no sistema.
     *
     * Antes de atualizar, verifica se os campos obrigatórios estão preenchidos corretamente e faz o upload da foto, caso seja fornecida.
     *
     * @param int $usuario_id ID do usuário a ser atualizado.
     * 
     * @param array $dados Associativo contendo as informações do usuário.
     *     - usuario_nome: string
     *     - usuario_email: string (precisa ser um e-mail válido)
     *     - usuario_telefone: string
     *     - usuario_senha: string (senha precisa ser criptografada com password_hash)
     *     - usuario_nivel: int
     *     - usuario_ativo: int (0 ou 1)
     *     - usuario_aniversario: string (data no formato 'YYYY-MM-DD')
     *     - usuario_cliente: int
     *     - usuario_foto: array (opcional) contendo 'tmp_name' e 'name' para o arquivo da foto
     *
     * @return array Retorna o status da operação, código HTTP e mensagem detalhando o resultado da operação.
     *     - status: string ('success', 'error', 'invalid_email', 'bad_request')
     *     - status_code: int (código HTTP relacionado)
     *     - message: string (mensagem descritiva da operação)
     */
    public function atualizarUsuario($usuario_id, $dados) {

        $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_nivel', 'usuario_ativo', 'usuario_aniversario'];

        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'status_code' => 400, 'message' => 'Email inválido.'];
        }

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'status_code' => 400, 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (isset($dados['foto']['tmp_name']) && !empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->uploadFile->salvarArquivo($this->pasta_foto, $dados['foto']);
            if ($uploadResult['status'] == 'upload_ok') {
                if ($dados['foto_link'] != null) {
                    unlink($dados['foto_link']);
                }
                $dados['usuario_foto'] = $this->pasta_foto . $uploadResult['filename'];
            } else {
                if ($uploadResult['status'] == 'error') {
                    return ['status' => 'error', 'message' => 'Erro ao fazer upload.'];
                }
            }
        } else {
            $dados['usuario_foto'] = null;
        }

        try {
            $this->usuarioModel->atualizar($usuario_id, $dados);
            return ['status' => 'success', 'status_code' => 200, 'message' => 'Usuário atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('user_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    /**
     * Lista os usuários associados a um cliente específico.
     *
     * Recupera todos os usuários associados a um cliente, ordenados pelo nome, e retorna o resultado em formato JSON.
     *
     * @param int $cliente ID do cliente para o qual os usuários serão listados.
     *
     * @return array Retorna o status da operação, código HTTP, mensagem e os dados dos usuários encontrados.
     *     - status: string ('success', 'empty', 'error')
     *     - status_code: int (código HTTP relacionado)
     *     - message: string (mensagem descritiva da operação)
     *     - dados: array (opcional) Lista de usuários associados ao cliente ou vazio se não houver registros.
     */
    public function listarUsuarios($cliente) {

        try {
            $usuarios = $this->usuarioModel->listar($cliente);

            if (empty($usuarios)) {
                return ['status' => 'empty', 'status_code' => 200, 'message' => 'Nenhum usuário registrado'];
            }

            return ['status' => 'success', 'status_code' => 200, 'message' => count($usuarios) . ' usuário(os) encontrado(os)', 'dados' => $usuarios];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('user_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    /**
     * Busca um usuário baseado em uma coluna específica e valor fornecido.
     *
     * Permite buscar um usuário pelo ID ou e-mail, conforme especificado pela coluna.
     *
     * @param string $coluna Nome da coluna pela qual será realizada a busca (usuario_id ou usuario_email).
     * @param string $valor Valor a ser buscado na coluna especificada.
     *
     * @return array Retorna o status da operação, código HTTP, mensagem e os dados do usuário encontrado ou não encontrado.
     *     - status: string ('success', 'bad_request', 'not_found', 'error')
     *     - status_code: int (código HTTP relacionado)
     *     - message: string (mensagem descritiva da operação)
     *     - dados: array (opcional) Informações do usuário encontrado ou vazio se não encontrado.
     */
    public function buscarUsuario($coluna, $valor) {

        $colunasPermitidas = ['usuario_id', 'usuario_email'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request', 'status_code' => 400, 'message' => 'Coluna inválida. Apenas usuario_id e usuario_email são permitidos.'];
        }

        try {
            $usuario = $this->usuarioModel->buscar($coluna, $valor);
            if ($usuario) {
                return ['status' => 'success', 'dados' => $usuario];
            } else {
                return ['status' => 'not_found',  'status_code' => 200, 'message' => 'Usuário não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('user_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }

    /**
     * Apaga um usuário do sistema com base no seu ID.
     *
     * Antes de apagar o usuário, verifica se há uma foto associada e tenta removê-la.
     * Também trata casos de dependências usando restrições de chaves estrangeiras.
     *
     * @param int $usuario_id ID do usuário a ser apagado.
     *
     * @return array Retorna o status da operação, código HTTP e mensagem detalhando o resultado da operação.
     *     - status: string ('success', 'error', 'not_found')
     *     - status_code: int (código HTTP relacionado)
     *     - message: string (mensagem descritiva da operação)
     */
    public function apagarUsuario($usuario_id) {
        try {

            $result = $this->buscarUsuario('usuario_id', $usuario_id);

            if ($result['status'] == 'not_found') {
                return $result;
            }

            if ($result['dados'][0]['usuario_foto'] != null) {
                unlink($result['dados'][0]['usuario_foto']);
            }

            $this->usuarioModel->apagar($usuario_id);
            return ['status' => 'success', 'status_code' => 200, 'message' => 'Usuário apagado com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 409, 'message' => 'Erro: Não é possível apagar o usuário. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('user_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'id_erro' => $erro_id];
        }
    }
}
