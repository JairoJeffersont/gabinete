<?php

namespace GabineteDigital\Controllers;

use GabineteDigital\Middleware\Logger;
use GabineteDigital\Middleware\GetJson;
use Exception;

class ReunioesController {

    private $logger;
    private $getjson;

    public function __construct() {
        $this->logger = new Logger();
        $this->getjson = new GetJson();
    }

    public function buscarReunioes($data) {

        $reunioesJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/eventos?dataInicio=' . $data . '&dataFim=' . $data . '&itens=100&ordem=ASC&ordenarPor=dataHoraInicio');

        if (isset($reunioesJson['error'])) {
            $this->logger->novoLog('reunioes_error', $reunioesJson['error']);
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }

        if (count($reunioesJson['dados']) > 0) {
            return ['status' => 'success', 'message' => 'Ok','dados' => $reunioesJson['dados']];
        } else {
            return ['status' => 'empty', 'message' => 'Sem reuniões para a data selecionada.', 'dados' => []];
        }


        
    }


    public function buscarPauta($id) {

        $pautaJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/eventos/'.$id.'/pauta');

        if (isset($pautaJson['error'])) {
            $this->logger->novoLog('reunioes_error', $pautaJson['error']);
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }

        if (isset($pautaJson['dados']) && count($pautaJson['dados']) > 0) {
            return ['status' => 'success', 'message' => 'Ok','dados' => $pautaJson['dados']];
        } else {
            return ['status' => 'empty', 'message' => 'Sem reuniões para a data selecionada.', 'dados' => []];
        }


        
    }
}
