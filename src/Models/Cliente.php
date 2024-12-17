<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

/**
 * Classe Cliente
 *
 * Representa as operações relacionadas à entidade Cliente no sistema.
 */
class Cliente {
    /**
     * Conexão com o banco de dados.
     *
     * @var PDO
     */
    private $conn;

    /**
     * Construtor da classe Cliente.
     *
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Cria um novo cliente no banco de dados.
     *
     * @param array $dados Associativo contendo as informações do cliente.
     *     - cliente_nome: string
     *     - cliente_email: string
     *     - cliente_telefone: string
     *     - cliente_ativo: int (0 ou 1)
     *     - cliente_assinaturas: int
     *     - cliente_deputado_id: int
     *     - cliente_deputado_nome: string
     *
     * @return bool Retorna true em caso de sucesso, ou false em caso de falha.
     */
    public function criar($dados) {
        $query = "INSERT INTO cliente (cliente_token, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_assinaturas, cliente_deputado_id, cliente_deputado_nome)
                  VALUES (:cliente_token, :cliente_nome, :cliente_email, :cliente_telefone, :cliente_ativo, :cliente_assinaturas, :cliente_deputado_id, :cliente_deputado_nome)";

        $stmt = $this->conn->prepare($query);
        $token = uniqid();

        $stmt->bindParam(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_assinaturas', $dados['cliente_assinaturas'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_id', $dados['cliente_deputado_id'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_nome', $dados['cliente_deputado_nome'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Atualiza um cliente existente no banco de dados.
     *
     * @param int $cliente_id ID do cliente a ser atualizado.
     * @param array $dados Associativo contendo as informações do cliente.
     *     - cliente_nome: string
     *     - cliente_email: string
     *     - cliente_telefone: string
     *     - cliente_ativo: int (0 ou 1)
     *     - cliente_assinaturas: int
     *     - cliente_deputado_id: int
     *     - cliente_deputado_nome: string
     *
     * @return bool Retorna true em caso de sucesso, ou false em caso de falha.
     */
    public function atualizar($cliente_id, $dados) {
        $query = "UPDATE cliente SET cliente_nome = :cliente_nome, cliente_email = :cliente_email, 
                  cliente_telefone = :cliente_telefone, cliente_ativo = :cliente_ativo, cliente_assinaturas = :cliente_assinaturas, cliente_deputado_id = :cliente_deputado_id, cliente_deputado_nome = :cliente_deputado_nome
                  WHERE cliente_id = :cliente_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_assinaturas', $dados['cliente_assinaturas'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_id', $dados['cliente_deputado_id'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_nome', $dados['cliente_deputado_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Lista todos os clientes do banco de dados.
     *
     * @return array Retorna um array associativo com os dados dos clientes.
     */
    public function listar() {
        $query = "SELECT * FROM cliente WHERE cliente_id <> 1 ORDER BY cliente_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca clientes no banco de dados com base em uma coluna e valor.
     *
     * @param string $coluna Nome da coluna para filtrar.
     * @param mixed $valor Valor a ser buscado na coluna.
     *
     * @return array Retorna um array associativo com os dados dos clientes encontrados.
     */
    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM cliente WHERE $coluna = :valor AND cliente_id <> 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Apaga um cliente do banco de dados.
     *
     * @param int $cliente_id ID do cliente a ser apagado.
     *
     * @return bool Retorna true em caso de sucesso, ou false em caso de falha.
     */
    public function apagar($cliente_id) {
        $query = "DELETE FROM cliente WHERE cliente_id = :cliente_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
