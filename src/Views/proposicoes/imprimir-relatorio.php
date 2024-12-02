<?php

include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';
$config = require '../src/Configs/config.php';

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$relatorio = $proposicaoController->gerarRelatorio();



?>

<script>
    window.print();

    window.onafterprint = function() {
        window.close();
    };
</script>

<style>
    body {
        background-image: none;
        font-size: 12px;
    }

    a {
        color: black;
    }

    #nota_texto_print p {
        text-indent: 5em;
        text-align: justify;
    }

    /* Forçar impressão em formato retrato */
    @media print {
        @page {
            size: portrait;
            /* Define o tamanho da página para retrato */
        }
    }
</style>

<div class="card mb-2 border-0 no-break">
    <div class="card-body p-2">
        <img src="./img/brasaooficialcolorido.png" style="width: 150px;" class="card-img-top mx-auto d-block" alt="...">
        <p class="card-text mb-0 text-center" style="font-size: 1.1em;">Câmara dos Deputados</p>
        <p class="card-text mb-4 text-center" style="font-size: 1em;">Gabinete do Deputado <?php echo $config['deputado']['nome'] ?></p>
        <p class="card-text mb-1 mt-2 text-center" style="font-size: 1.4em;"><b>Lista completa de proposições</b></p>

    </div>
</div>

<div class="card mb-2 border-0  no-break">
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped mb-0 custom-table">

                <tbody>
                    <?php
                    $busca = $proposicaoController->gerarRelatorio();

                    if ($busca['status'] == 'success') {
                        // Agrupar proposições por ano e por tipo
                        $grupos = [];
                        foreach ($busca['dados'] as $proposicao) {
                            $grupos[$proposicao['proposicao_ano']][$proposicao['proposicao_tipo']][] = $proposicao;
                        }

                        // Ordenar proposições em cada grupo por 'proposicao_numero' em ordem decrescente
                        foreach ($grupos as $ano => &$tipos) { // Referência para alterar diretamente o array
                            foreach ($tipos as $tipo => &$proposicoes) {
                                usort($proposicoes, function ($a, $b) {
                                    return $b['proposicao_numero'] <=> $a['proposicao_numero'];
                                });
                            }
                        }
                        unset($tipos, $proposicoes); // Prevenir alterações após o loop

                        // Renderizar as tabelas agrupadas por ano e tipo
                        foreach ($grupos as $ano => $tipos) {
                            echo "<tr><th colspan='2' class='bg-success text-white p-3'><b>Ano: {$ano}</b></th></tr>";
                            foreach ($tipos as $tipo => $proposicoes) {
                                echo "<tr><th colspan='2' class='bg-secondary text-white p-2'>Tipo: {$tipo}</th></tr>";
                                foreach ($proposicoes as $proposicao) {
                                    echo '<tr>';
                                    echo '<td style="white-space: nowrap;">';
                                    echo '<a href="?secao=proposicao&id=' . $proposicao['proposicao_id'] . '">';
                                    echo $proposicao['proposicao_titulo'];
                                    echo '</a>';
                                    echo '</td>';

                                    $nota = $notaController->buscarNotaTecnica('nota_proposicao', $proposicao['proposicao_id']);

                                    if ($nota['status'] == 'success') {
                                        echo '<td><b>' . $nota['dados'][0]['nota_titulo'] . '</b><br>' . $nota['dados'][0]['nota_resumo'];
                                    } else {
                                        echo '<td>' . $proposicao['proposicao_ementa'];
                                    }

                                    if (!empty($proposicao['proposicao_principal_titulo'])) {
                                        echo '<p class="mb-0 mt-1"><em><small><i class="bi bi-info-circle"></i> Esse projeto foi apensado ao: <b>' . $proposicao['proposicao_principal_titulo'] . '</b></small></p>';
                                    }

                                    echo '</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                    } elseif (in_array($busca['status'], ['empty', 'error'])) {
                        echo '<tr><td colspan="6">' . $busca['message'] . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>

        </div>

    </div>
</div>