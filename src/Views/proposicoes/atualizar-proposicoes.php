<?php

include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';

use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

//print_r($proposicaoController->inserirProposicoesAutores(2019));

?>