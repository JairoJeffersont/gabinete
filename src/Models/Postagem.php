<?php

namespace GabineteDigital\Models;

use GabineteDigital\Middleware\Database;
use PDO;

class Postagem {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function criar($dados) {
        $query = "INSERT INTO postagens 
                  (postagem_titulo, postagem_data, postagem_pasta, postagem_informacoes, postagem_midias, postagem_status, postagem_criada_por)
                  VALUES 
                  (:postagem_titulo, :postagem_data, :postagem_pasta, :postagem_informacoes, :postagem_midias, :postagem_status, :postagem_criada_por)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':postagem_titulo', $dados['postagem_titulo']);
        $stmt->bindParam(':postagem_data', $dados['postagem_data']);
        $stmt->bindParam(':postagem_pasta', $dados['postagem_pasta']);
        $stmt->bindParam(':postagem_informacoes', $dados['postagem_informacoes']);
        $stmt->bindParam(':postagem_midias', $dados['postagem_midias']);
        $stmt->bindParam(':postagem_status', $dados['postagem_status'], PDO::PARAM_INT);
        $stmt->bindParam(':postagem_criada_por', $dados['postagem_criada_por'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function atualizar($postagem_id, $dados) {
        $query = "UPDATE postagens 
                  SET postagem_titulo = :postagem_titulo, 
                      postagem_data = :postagem_data, 
                      postagem_pasta = :postagem_pasta, 
                      postagem_informacoes = :postagem_informacoes, 
                      postagem_midias = :postagem_midias, 
                      postagem_status = :postagem_status 
                  WHERE postagem_id = :postagem_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':postagem_titulo', $dados['postagem_titulo']);
        $stmt->bindParam(':postagem_data', $dados['postagem_data']);
        $stmt->bindParam(':postagem_pasta', $dados['postagem_pasta']);
        $stmt->bindParam(':postagem_informacoes', $dados['postagem_informacoes']);
        $stmt->bindParam(':postagem_midias', $dados['postagem_midias']);
        $stmt->bindParam(':postagem_status', $dados['postagem_status'], PDO::PARAM_INT);
        $stmt->bindParam(':postagem_id', $postagem_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function listar() {
        $query = "SELECT * FROM postagens ORDER BY postagem_titulo ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM postagens WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function apagar($postagem_id) {
        $query = "DELETE FROM postagens WHERE postagem_id = :postagem_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':postagem_id', $postagem_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
