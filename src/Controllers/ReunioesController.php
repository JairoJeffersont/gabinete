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

    public function buscarReunioes($data, $tipo, $situacao) {

        if ($situacao == 0) {
            $reunioesJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/eventos?codTipoEvento=' . $tipo . '&dataInicio=' . $data . '&dataFim=' . $data . '&itens=100&ordem=ASC&ordenarPor=dataHoraInicio');

        } else {
            $reunioesJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/eventos?codTipoEvento=' . $tipo . '&codSituacao=' . $situacao . '&dataInicio=' . $data . '&dataFim=' . $data . '&itens=100&ordem=ASC&ordenarPor=dataHoraInicio');
        }


        if (isset($reunioesJson['error'])) {
            $this->logger->novoLog('reunioes_error', $reunioesJson['error']);
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }

        if (count($reunioesJson['dados']) > 0) {
            $reunioesAgrupadas = [];

            foreach ($reunioesJson['dados'] as $reuniao) {
                $horaInicio = substr($reuniao['dataHoraInicio'], 0, 16);

                if (!isset($reunioesAgrupadas[$horaInicio])) {
                    $reunioesAgrupadas[$horaInicio] = [];
                }
                $reunioesAgrupadas[$horaInicio][] = $reuniao;
            }

            ksort($reunioesAgrupadas);

            return [
                'status' => 'success',
                'message' => 'Ok',
                'dados' => $reunioesAgrupadas
            ];
        } else {
            return ['status' => 'empty', 'message' => 'Sem reuniões para a data selecionada.', 'dados' => []];
        }
    }


    public function buscarTipos() {

        $pautaJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/referencias/tiposEvento');

        if (isset($pautaJson['error'])) {
            $this->logger->novoLog('reunioes_error', $pautaJson['error']);
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }

        if (isset($pautaJson['dados']) && count($pautaJson['dados']) > 0) {
            return ['status' => 'success', 'message' => 'Ok', 'dados' => $pautaJson['dados']];
        } else {
            return ['status' => 'empty', 'message' => 'Sem tipos encontrados', 'dados' => []];
        }
    }

    public function buscarSituacoes() {

        $pautaJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/referencias/situacoesEvento');

        if (isset($pautaJson['error'])) {
            $this->logger->novoLog('reunioes_error', $pautaJson['error']);
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }

        if (isset($pautaJson['dados']) && count($pautaJson['dados']) > 0) {
            return ['status' => 'success', 'message' => 'Ok', 'dados' => $pautaJson['dados']];
        } else {
            return ['status' => 'empty', 'message' => 'Sem situações', 'dados' => []];
        }
    }


    public function buscarPauta($id) {

        $pautaJson = $this->getjson->getJson('https://dadosabertos.camara.leg.br/api/v2/eventos/' . $id . '/pauta');

        if (isset($pautaJson['error'])) {
            $this->logger->novoLog('reunioes_error', $pautaJson['error']);
            return ['status' => 'error', 'message' => 'Erro interno do servidor'];
        }

        if (isset($pautaJson['dados']) && count($pautaJson['dados']) > 0) {
            return ['status' => 'success', 'message' => 'Ok', 'dados' => $pautaJson['dados']];
        } else {
            return ['status' => 'empty', 'message' => 'Sem reuniões para a data selecionada.', 'dados' => []];
        }
    }
}
