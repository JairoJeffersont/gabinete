<?php

namespace GabineteDigital\Middleware;

use GabineteDigital\Middleware\Logger;

use PDO;
use PDOException;


/**
 * Classe responsável por gerenciar a conexão com o banco de dados.
 */
class Database {
    /**
     * @var PDO|null Armazena a conexão com o banco de dados.
     */
    private $connection;

    /**
     * @var Logger Instância do gerenciador de logs.
     */
    private $logger;

    /**
     * Construtor da classe.
     *
     * Configura a conexão com o banco de dados usando as informações do arquivo de configuração.
     * Caso ocorra um erro na conexão, registra o log e retorna uma resposta de erro.
     */
    public function __construct() {
        $config = require dirname(__DIR__, 2) . '/src/Configs/config.php';

        $this->logger = new Logger();

        $host = $config['database']['host'];
        $dbname = $config['database']['name'];
        $username = $config['database']['user'];
        $password = $config['database']['password'];

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->exec("SET NAMES 'utf8mb4'");
        } catch (PDOException $e) {
            $this->logger->novoLog('db_error', $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);

            $response = ['status' => 'db_not_connected', 'status_code' => 500, 'message' => "Banco de dados não conectado"];

            echo json_encode($response);
            exit;
        }
    }

    /**
     * Retorna a conexão com o banco de dados.
     *
     * @return PDO|null Retorna a instância do PDO ou null caso não esteja conectado.
     */
    public function getConnection() {
        return $this->connection;
    }
}
