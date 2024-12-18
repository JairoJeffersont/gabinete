<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;


class Usuario {
    private $conn;


    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }


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


    public function listar($cliente = null) {
        if ($cliente == null) {
            $query = "SELECT * FROM view_usuarios ORDER BY usuario_nome ASC";
        } else {
            $query = "SELECT * FROM view_usuarios WHERE usuario_cliente =:usuario_cliente ORDER BY usuario_nome ASC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_cliente', $cliente);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_usuarios WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function apagar($usuario_id) {
        $query = "DELETE FROM usuario WHERE usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
