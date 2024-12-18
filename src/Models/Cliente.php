<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;


class Cliente {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function criar($dados) {
        $query = "INSERT INTO cliente (cliente_token, cliente_nome, cliente_email, cliente_telefone, cliente_endereco, cliente_cep, cliente_cpf_cnpj, cliente_ativo, cliente_assinaturas, cliente_deputado_id, cliente_deputado_nome, cliente_deputado_estado)
                  VALUES (:cliente_token, :cliente_nome, :cliente_email, :cliente_telefone, :cliente_endereco, :cliente_cep, :cliente_cpf_cnpj, :cliente_ativo, :cliente_assinaturas, :cliente_deputado_id, :cliente_deputado_nome, :cliente_deputado_estado)";

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
        $stmt->bindParam(':cliente_cpf_cnpj', $dados['cliente_cpf_cnpj'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_cep', $dados['cliente_cep'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_endereco', $dados['cliente_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_deputado_estado', $dados['cliente_deputado_estado'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizar($cliente_id, $dados) {
        $query = "UPDATE cliente SET cliente_nome = :cliente_nome, cliente_email = :cliente_email, 
                  cliente_telefone = :cliente_telefone, cliente_ativo = :cliente_ativo, cliente_assinaturas = :cliente_assinaturas, cliente_endereco = :cliente_endereco, cliente_cep = :cliente_cep, cliente_cpf_cnpj = :cliente_cpf_cnpj, cliente_deputado_id = :cliente_deputado_id, cliente_deputado_nome = :cliente_deputado_nome, cliente_deputado_estado = :cliente_deputado_estado
                  WHERE cliente_id = :cliente_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_assinaturas', $dados['cliente_assinaturas'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_id', $dados['cliente_deputado_id'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_deputado_nome', $dados['cliente_deputado_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_cpf_cnpj', $dados['cliente_cpf_cnpj'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_cep', $dados['cliente_cep'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente_endereco', $dados['cliente_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_deputado_estado', $dados['cliente_deputado_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function listar() {
        $query = "SELECT * FROM cliente WHERE cliente_id <> 1 ORDER BY cliente_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM cliente WHERE $coluna = :valor AND cliente_id <> 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function apagar($cliente_id) {
        $query = "DELETE FROM cliente WHERE cliente_id = :cliente_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
