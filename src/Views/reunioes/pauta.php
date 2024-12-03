<?php

use GabineteDigital\Controllers\ReunioesController;

include '../src/Views/includes/verificaLogado.php';
require_once '../autoloader.php';

$reunioesController = new ReunioesController();


$reuniaoId = $_GET['reuniao'];

$buscaReunioes = $reunioesController->buscarPauta($reuniaoId);

if ($buscaReunioes['status'] == 'empty' || $buscaReunioes['status'] == 'error') {
    header('Location: ?secao=reunioes');
}

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="#" onclick="history.back(-1)" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text"></i> Pauta da Reunião/Sessão</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Consulta a pauta de votações da reunião desejada</p>
                </div>
            </div>

            <div class="card mb-2 card-description ">

                <div class="card-body p-2">
                    <div class="accordion" id="accordionPanelsStayOpenExample">
                        <?php
                        foreach ($buscaReunioes['dados'] as $proposicaoPauta) {
                            echo '<div class="accordion-item">
                                    <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" style="font-size:12px" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $proposicaoPauta['ordem'] . '" aria-expanded="false" aria-controls="panelsStayOpen-collapse' . $proposicaoPauta['ordem'] . '">
                                        ' . $proposicaoPauta['ordem'] . ' - ' . $proposicaoPauta['titulo'] . '
                                    </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapse' . $proposicaoPauta['ordem'] . '" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <p class="mb-1"><i class="bi bi-dot"></i> Regime de tramitação: <b>' . $proposicaoPauta['regime'] . '</b></p>
                                        <p class="mb-0"><i class="bi bi-dot"></i> <a href="?secao=proposicao&id='.(empty($proposproposicaoPautaicao['proposicaoRelacionada_']) ? $proposicaoPauta['proposicao_']['id'] : $proposicaoPauta['proposicaoRelacionada_']['id']).'">Ficha de tramitação</a></p>
                                    </div>
                                    </div>
                                </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>