<?php

include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';
$config = require '../src/Configs/config.php';

use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

$itens = isset($_GET['itens']) ? $_GET['itens'] : 10;
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) ? $_GET['ordenarPor'] : 'proposicao_id';
$ordem = isset($_GET['ordem']) ? $_GET['ordem'] : 'desc';
$termo = isset($_GET['termo']) ? $_GET['termo'] : '';
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'PL';
$arquivada = isset($_GET['arquivada']) ? filter_var($_GET['arquivada'], FILTER_VALIDATE_BOOLEAN) : false;

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=imprimir-proposicoes&pagina=1&ano=<?php echo $ano ?>&tipo=<?php echo $tipo ?>&arquivada=<?php echo $arquivada ?>&termo=<?php echo $termo ?>" target="_blank" role="button"><i class="bi bi-printer-fill"></i> Imprimir</a>
                </div>
            </div>
            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text-fill"></i> Proposições do gabinete</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Proposições de autoria do gabinete | <b><?php echo $ano ?></b> | <b><?php echo $tipo ?></b> | <b><?php echo ($arquivada) ? 'Arquivadas' : 'Em tramitação'; ?></b></p>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-1 col-3">
                                    <input type="hidden" name="secao" value="proposicoes" />
                                    <select class="form-select form-select-sm" name="ano" required>
                                        <?php
                                        for ($anoSelect = $config['deputado']['ano_primeiro_mandato']; $anoSelect <= date('Y'); $anoSelect++) {
                                            if ($anoSelect == $ano) {
                                                echo '<option value="' . $anoSelect . '" selected>' . $anoSelect . '</option>';
                                            } else {
                                                echo '<option value="' . $anoSelect . '">' . $anoSelect . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-1 col-3">
                                    <select class="form-select form-select-sm" name="tipo" required>
                                        <option value="PL" <?php echo $tipo == 'PL' ? 'selected' : ''; ?>>PL</option>
                                        <option value="REQ" <?php echo $tipo == 'REQ' ? 'selected' : ''; ?>>REQ</option>
                                    </select>
                                </div>
                                <div class="col-md-1 col-6">
                                    <select class="form-select form-select-sm" name="itens" required>
                                        <option value="5" <?php echo $itens == '5' ? 'selected' : ''; ?>>5 itens</option>
                                        <option value="10" <?php echo $itens == '10' ? 'selected' : ''; ?>>10 itens</option>
                                        <option value="25" <?php echo $itens == '25' ? 'selected' : ''; ?>>25 itens</option>
                                        <option value="50" <?php echo $itens == '50' ? 'selected' : ''; ?>>50 itens</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12">
                                    <select class="form-select form-select-sm" name="arquivada" required>
                                        <option value="false" <?php echo $arquivada === false ? 'selected' : ''; ?>>Em tramitação</option>
                                        <option value="true" <?php echo $arquivada === true ? 'selected' : ''; ?>>Arquivada</option>
                                    </select>
                                </div>

                                <div class="col-md-3 col-10">
                                    <input type="text" class="form-control form-control-sm" name="termo" value="<?php echo $termo ?>" placeholder="Buscar...">
                                </div>
                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Titulo</th>
                                    <th scope="col">Ementa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $busca = $proposicaoController->proposicoesGabinete($itens, $pagina, $ordenarPor, $ordem, $tipo, $ano, $termo, $arquivada);
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $proposicao) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=proposicao&id=' . $proposicao['proposicao_id'] . '">' . $proposicao['proposicao_titulo'] . '</a></td>';
                                        echo '<td>' . $proposicao['proposicao_ementa'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty' || $busca['status'] == 'error') {
                                    echo '<tr><td colspan="6">' . $busca['message'] . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php

                    $totalPagina = $busca['total_paginas'] ?? 0;

                    if ($totalPagina > 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                        for ($i = 1; $i <= $totalPagina; $i++) {
                            echo '<li class="page-item ' . ($pagina == $i ? 'active' : '') . '">';
                            echo '<a class="page-link" href="?secao=proposicoes&itens=' . $itens . '&pagina=' . $i . '&ano=' . $ano . '&tipo=' . $tipo . '&arquivada=' . $arquivada . '">' . $i . '</a>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    }

                    ?>

                </div>
            </div>
        </div>
    </div>
</div>