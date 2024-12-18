<?php

namespace GabineteDigital\Middleware;

use GabineteDigital\Middleware\Logger;

use PDO;
use PDOException;



class Database {
  
    private $connection;
  
    private $logger;
   
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
            header('Location: ?secao=fatal_error');
            exit;
        }
    }

 
    public function getConnection() {
        return $this->connection;
    }
}
