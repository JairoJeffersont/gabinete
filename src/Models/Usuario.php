<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe Usuario
 *
 * Gerencia operações relacionadas a usuários, como criação, atualização, listagem, busca e remoção.
 */
class Usuario {
    private $conn;

    /**
     * Construtor da classe Usuario.
     *
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Cria um novo usuário no banco de dados.
     *
     * @param array $dados Associativo contendo as informações do usuário.
     *     - usuario_nome: string
     *     - usuario_email: string
     *     - usuario_telefone: string
     *     - usuario_senha: string (senha precisa ser criptografada com password_hash)
     *     - usuario_nivel: int
     *     - usuario_ativo: int (0 ou 1)
     *     - usuario_aniversario: string (data no formato 'YYYY-MM-DD')
     *     - usuario_cliente: int
     *     - usuario_foto: string (opcional)
     *
     * @return bool Retorna `true` se a inserção for bem-sucedida, `false` caso contrário.
     */
    public function criar($dados) {
        $query = "INSERT INTO usuario (usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_nivel, usuario_ativo, usuario_aniversario, usuario_cliente ,usuario_foto)
                  VALUES (:usuario_nome, :usuario_email, :usuario_telefone, :usuario_senha, :usuario_nivel, :usuario_ativo, :usuario_aniversario, :usuario_cliente ,:usuario_foto)";

        $stmt = $this->conn->prepare($query);

        $senha_hash = password_hash($dados['usuario_senha'], PASSWORD_BCRYPT);

        $stmt->bindParam(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_senha', $senha_hash, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_nivel', $dados['usuario_nivel'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_cliente', $dados['usuario_cliente'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_foto', $dados['usuario_foto'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Atualiza as informações de um usuário existente.
     *
     * @param int $usuario_id ID do usuário a ser atualizado.
     * @param array $dados Associativo contendo as informações do usuário.
     *     - usuario_nome: string
     *     - usuario_email: string
     *     - usuario_telefone: string
     *     - usuario_nivel: int
     *     - usuario_ativo: int (0 ou 1)
     *     - usuario_aniversario: string (data no formato 'YYYY-MM-DD')
     *     - usuario_foto: string (opcional)
     *
     * @return bool Retorna `true` se a atualização for bem-sucedida, `false` caso contrário.
     */
    public function atualizar($usuario_id, $dados) {
        if (isset($dados['usuario_foto']) && !empty($dados['usuario_foto'])) {
            $query = "UPDATE usuario SET usuario_nome = :usuario_nome, usuario_email = :usuario_email, usuario_telefone = :usuario_telefone, usuario_nivel = :usuario_nivel, usuario_ativo = :usuario_ativo, usuario_aniversario = :usuario_aniversario, usuario_foto = :usuario_foto WHERE usuario_id = :usuario_id";
        } else {
            $query = "UPDATE usuario SET usuario_nome = :usuario_nome, usuario_email = :usuario_email, usuario_telefone = :usuario_telefone, usuario_nivel = :usuario_nivel, usuario_ativo = :usuario_ativo, usuario_aniversario = :usuario_aniversario WHERE usuario_id = :usuario_id";
        }

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':usuario_nivel', $dados['usuario_nivel'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);
        if (isset($dados['usuario_foto']) && !empty($dados['usuario_foto'])) {
            $stmt->bindParam(':usuario_foto', $dados['usuario_foto'], PDO::PARAM_STR);
        }
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Lista todos os usuários relacionados a um cliente específico.
     *
     * @param int $cliente ID do cliente.
     *
     * @return array Retorna um array de usuários associados ao cliente ou vazio caso não existam usuários.
     */
    public function listar($cliente) {
        $query = "SELECT * FROM usuario WHERE usuario_cliente =:usuario_cliente AND usuario_id <> 1 ORDER BY usuario_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_cliente', $cliente);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário baseado em uma coluna e valor.
     *
     * Apenas usuario_id e usuario_email são permitidos como colunas para pesquisa.
     *
     * @param string $coluna Nome da coluna a ser pesquisada.
     * @param mixed $valor Valor a ser buscado na coluna.
     *
     * @return array Retorna um array com o usuário encontrado ou vazio caso não existam usuários.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM usuario WHERE $coluna = :valor AND usuario_id <> 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Apaga um usuário do banco de dados.
     *
     * @param int $usuario_id ID do usuário a ser apagado.
     *
     * @return bool Retorna `true` se a exclusão for bem-sucedida, `false` caso contrário.
     */
    public function apagar($usuario_id) {
        $query = "DELETE FROM usuario WHERE usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
