<?php

ini_set('memory_limit', '512M'); // Define o limite de memória
ini_set('max_execution_time', 300); // Define o tempo máximo de execução


include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';

use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

//print_r($proposicaoController->inserirProposicoesAutores(2019));

?>