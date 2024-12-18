<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;


class Orgao {
    private $conn;


    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function criar($dados) {
        $query = "INSERT INTO orgaos (orgao_nome, orgao_email, orgao_telefone, orgao_endereco, orgao_bairro, orgao_municipio, orgao_estado, orgao_cep, orgao_tipo, orgao_informacoes, orgao_site, orgao_criado_por, orgao_cliente)
                  VALUES (:orgao_nome, :orgao_email, :orgao_telefone, :orgao_endereco, :orgao_bairro, :orgao_municipio, :orgao_estado, :orgao_cep, :orgao_tipo, :orgao_informacoes, :orgao_site, :orgao_criado_por, :orgao_cliente)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':orgao_nome', $dados['orgao_nome']);
        $stmt->bindParam(':orgao_email', $dados['orgao_email']);
        $stmt->bindParam(':orgao_telefone', $dados['orgao_telefone']);
        $stmt->bindParam(':orgao_endereco', $dados['orgao_endereco']);
        $stmt->bindParam(':orgao_bairro', $dados['orgao_bairro']);
        $stmt->bindParam(':orgao_municipio', $dados['orgao_municipio']);
        $stmt->bindParam(':orgao_estado', $dados['orgao_estado']);
        $stmt->bindParam(':orgao_cep', $dados['orgao_cep']);
        $stmt->bindParam(':orgao_tipo', $dados['orgao_tipo'], PDO::PARAM_INT);
        $stmt->bindParam(':orgao_informacoes', $dados['orgao_informacoes']);
        $stmt->bindParam(':orgao_site', $dados['orgao_site']);
        $stmt->bindParam(':orgao_criado_por', $dados['orgao_criado_por'], PDO::PARAM_INT);
        $stmt->bindParam(':orgao_cliente', $dados['orgao_cliente'], PDO::PARAM_INT);


        return $stmt->execute();
    }

    public function atualizar($orgao_id, $dados) {
        $query = "UPDATE orgaos SET orgao_nome = :orgao_nome, orgao_email = :orgao_email, orgao_telefone = :orgao_telefone, orgao_endereco = :orgao_endereco, orgao_bairro = :orgao_bairro, orgao_municipio = :orgao_municipio, orgao_estado = :orgao_estado, orgao_cep = :orgao_cep, orgao_tipo = :orgao_tipo, orgao_informacoes = :orgao_informacoes, orgao_site = :orgao_site WHERE orgao_id = :orgao_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':orgao_nome', $dados['orgao_nome']);
        $stmt->bindParam(':orgao_email', $dados['orgao_email']);
        $stmt->bindParam(':orgao_telefone', $dados['orgao_telefone']);
        $stmt->bindParam(':orgao_endereco', $dados['orgao_endereco']);
        $stmt->bindParam(':orgao_bairro', $dados['orgao_bairro']);
        $stmt->bindParam(':orgao_municipio', $dados['orgao_municipio']);
        $stmt->bindParam(':orgao_estado', $dados['orgao_estado']);
        $stmt->bindParam(':orgao_cep', $dados['orgao_cep']);
        $stmt->bindParam(':orgao_tipo', $dados['orgao_tipo'], PDO::PARAM_INT);
        $stmt->bindParam(':orgao_informacoes', $dados['orgao_informacoes']);
        $stmt->bindParam(':orgao_site', $dados['orgao_site']);
        $stmt->bindParam(':orgao_id', $orgao_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente) {
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        if ($termo === null) {
            if ($estado != null) {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_estado = '" . $estado . "' AND orgao_cliente = :cliente) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_estado = '" . $estado . "'  AND orgao_cliente = :cliente ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            } else {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1) AS total FROM view_orgaos WHERE orgao_id <> 1  AND orgao_cliente = :cliente ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            }
        } else {
            if ($estado != null) {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_estado = '" . $estado . "'  AND orgao_cliente = :cliente) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_estado = '" . $estado . "'  AND orgao_cliente = :cliente ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
                $termo = '%' . $termo . '%';
            } else {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo  AND orgao_cliente = :cliente) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_cliente = :cliente ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
                $termo = '%' . $termo . '%';
            }
        }


        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':cliente', $cliente, PDO::PARAM_INT);

        if ($termo !== null) {
            $stmt->bindValue(':termo', $termo, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_orgaos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function apagar($orgao_id) {
        $query = "DELETE FROM orgaos WHERE orgao_id = :orgao_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orgao_id', $orgao_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
