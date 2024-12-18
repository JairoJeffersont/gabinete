<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

class OrgaoTipo {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function criar($dados) {
        $query = "INSERT INTO orgaos_tipos (orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_cliente)
                  VALUES (:orgao_tipo_nome, :orgao_tipo_descricao, :orgao_tipo_criado_por, :orgao_tipo_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':orgao_tipo_nome', $dados['orgao_tipo_nome']);
        $stmt->bindParam(':orgao_tipo_descricao', $dados['orgao_tipo_descricao']);
        $stmt->bindParam(':orgao_tipo_criado_por', $dados['orgao_tipo_criador_por'], PDO::PARAM_INT);
        $stmt->bindParam(':orgao_tipo_cliente', $dados['orgao_tipo_cliente']);


        return $stmt->execute();
    }

    public function atualizar($orgao_tipo_id, $dados) {
        $query = "UPDATE orgaos_tipos 
                  SET orgao_tipo_nome = :orgao_tipo_nome, 
                      orgao_tipo_descricao = :orgao_tipo_descricao
                  WHERE orgao_tipo_id = :orgao_tipo_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':orgao_tipo_nome', $dados['orgao_tipo_nome']);
        $stmt->bindParam(':orgao_tipo_descricao', $dados['orgao_tipo_descricao']);
        $stmt->bindParam(':orgao_tipo_id', $orgao_tipo_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function listar($cliente) {
        $query = "SELECT * FROM view_orgaos_tipos WHERE orgao_tipo_cliente = :usuario_cliente OR orgao_tipo_criado_por = 1 ORDER BY orgao_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_cliente', $cliente);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_orgaos_tipos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function apagar($orgao_tipo_id) {
        $query = "DELETE FROM orgaos_tipos WHERE orgao_tipo_id = :orgao_tipo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orgao_tipo_id', $orgao_tipo_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
