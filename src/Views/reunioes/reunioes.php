<?php

use GabineteDigital\Controllers\ReunioesController;

include '../src/Views/includes/verificaLogado.php';
require_once '../autoloader.php';

$reunioesController = new ReunioesController();

$data = $_GET['data'] ?? date('Y-m-d');
$tipo = $_GET['tipo'] ?? 112;
$situacao = $_GET['situacao'] ?? 3;

$buscaReunioes = $reunioesController->buscarReunioes($data, $tipo, $situacao);

//print_r($buscaReunioes);

?>


<div class="d-flex" id="wrapper">
    <?php include '../src/Views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">

            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>

            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-calendar3"></i> Reuniões e sessões do dia</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Consulte todas as reuniões sessões da Câmara </p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="secao" value="reunioes" />
                        <div class="col-md-1 col-4">
                            <input type="date" class="form-control form-control-sm" name="data" value="<?php echo $data ?>">
                        </div>
                        <div class="col-md-2 col-8">
                            <select class="form-select form-select-sm" name="tipo" required>
                                <?php
                                $buscaTipos = $reunioesController->buscarTipos();
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['dados'] as $tipoOption) {
                                        if ($tipoOption['cod'] == $tipo) {
                                            echo '<option value="' . $tipoOption['cod'] . '" selected>' . $tipoOption['nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipoOption['cod'] . '">' . $tipoOption['nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-10">
                            <select class="form-select form-select-sm" name="situacao" required>
                                <option value="0" <?php echo ($situacao == 0) ? 'selected' : ''; ?>>Todas</option>

                                <?php
                                $buscaSituacoes = $reunioesController->buscarSituacoes();
                                if ($buscaSituacoes['status'] == 'success') {
                                    foreach ($buscaSituacoes['dados'] as $situacaoOption) {
                                        if ($situacaoOption['cod'] == $situacao) {
                                            echo '<option value="' . $situacaoOption['cod'] . '" selected>' . $situacaoOption['nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $situacaoOption['cod'] . '">' . $situacaoOption['nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-2 card-description ">
                <div class="card-body p-2">
                    <div class="accordion" id="accordionPanelsStayOpenExample">
                        <?php

                        if ($buscaReunioes['status'] == 'success') {
                            foreach ($buscaReunioes['dados'] as $index => $comissao) {
                                echo '<div class="accordion-item">';
                                echo '<h2 class="accordion-header shadow-sm">
                                        <button class="accordion-button collapsed" type="button" style="font-size:14px" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $index . '" aria-expanded="false" aria-controls="panelsStayOpen-collapse' . $index . '">
                                            ' . $index . ' | ' . $comissao[0]['orgaos'][0]['nomePublicacao'] . '
                                        </button>
                                      </h2>';
                                echo '<div id="panelsStayOpen-collapse' . $index . '" class="accordion-collapse collapse">';
                                echo '<div class="accordion-body p-2 shadow-sm">';

                                foreach ($comissao as $reuniao) {
                                    echo '<div class="card mb-2 shadow-sm">';
                                    echo '<div class="card-body">';
                                    echo '<div class="card-text mb-0">' . $reuniao['descricao'] . '</div><hr>';
                                    echo '<div class="card-text mb-2"><i class="bi bi-house-fill"></i> ' . $reuniao['localCamara']['nome'] . ' </div>';
                                    echo '<div class="card-text mb-2"><i class="bi bi-exclamation-circle-fill"></i> <b>' . $reuniao['situacao'] . ' </b></div>';
                                    echo '<div class="card-text mb-0"><i class="bi bi-alarm"></i> Hora início: <b>' . date('H:i', strtotime($reuniao['dataHoraInicio'])) . '</b></div>';
                                    if (!empty($reuniao['dataHoraFim'])) {
                                        echo '<div class="card-text mb-0"><i class="bi bi-alarm"></i> Hora fim: <b>' . date('H:i', strtotime($reuniao['dataHoraFim'])) . '</b></div><hr>';
                                    } else {
                                        echo '<hr>';
                                    }
                                    echo '<div class="btn-group" role="group" aria-label="Basic example">';
                                    if (!empty($reuniao['urlRegistro'])) {
                                        echo '<a href="' . $reuniao['urlRegistro'] . '" type="button" target="_blank" class="btn btn-danger btn-sm" style="font-size: 0.9em"><i class="bi bi-youtube"></i> Youtube</a>';
                                    } else {
                                        echo '<button type="button" class="btn btn-danger btn-sm disabled" style="font-size: 0.9em"><i class="bi bi-youtube"></i> Youtube</button>';
                                    }

                                    echo '<a href="https://www.camara.leg.br/evento-legislativo/' . $reuniao['id'] . '" target="_blank" type="button" class="btn btn-success btn-sm" style="font-size: 0.9em"><i class="bi bi-file-earmark-text-fill"></i> Página da CD</a>';

                                    if ($tipo == 112 || $tipo == 110) {
                                        echo '<a href="?secao=pauta&reuniao=' . $reuniao['id'] . '" type="button" class="btn btn-secondary btn-sm" style="font-size: 0.9em"><i class="bi bi-file-earmark-text-fill"></i> Ver Pauta</a>';
                                    } else {
                                        echo '<button type="button" class="btn btn-primary disabled btn-sm" style="font-size: 0.9em"><i class="bi bi-file-earmark-text-fill"></i> Ver Pauta</button>';
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="card-text">' . $buscaReunioes['message'] . '</div>';
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>