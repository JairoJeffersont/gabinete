<?php

namespace GabineteDigital\Controllers;
use GabineteDigital\Middleware\Logger;
use GabineteDigital\Middleware\GetJson;

class ReunioesController{

    private $logger;
    private $getjson;

    public function __construct() {
        $this->logger = new Logger();
        $this->getjson = new GetJson();
    }

    public function buscarReunioes($data){
        $proposicoesJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/eventos?dataInicio='.$data.'&dataFim='.$data.'&itens=100&ordem=ASC&ordenarPor=dataHoraInicio');
        $dados = [];

        return $proposicoesJson;

    }

}