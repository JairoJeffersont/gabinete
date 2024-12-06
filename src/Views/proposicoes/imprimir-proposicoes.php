<?php

include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';
$config = require '../src/Configs/config.php';

use GabineteDigital\Controllers\ProposicaoController;
use GabineteDigital\Controllers\NotaTecnicaController;


$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();


$itens = isset($_GET['itens']) ? $_GET['itens'] : 1000;
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) ? $_GET['ordenarPor'] : 'proposicao_id';
$ordem = isset($_GET['ordem']) ? $_GET['ordem'] : 'desc';
$termo = isset($_GET['termo']) ? $_GET['termo'] : '';
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'PL';
$arquivada = isset($_GET['arquivada']) ? filter_var($_GET['arquivada'], FILTER_VALIDATE_BOOLEAN) : false;
$notas = $notaController->listarNotasTecnicas();

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
            size: landscape;
            /* Define o tamanho da página para retrato */
        }
    }
</style>


<div class="card mb-2 border-0 no-break">
    <div class="card-body p-2">
        <img src="./img/brasaooficialcolorido.png" style="width: 150px;" class="card-img-top mx-auto d-block" alt="...">
        <p class="card-text mb-0 text-center" style="font-size: 1.1em;">Câmara dos Deputados</p>
        <p class="card-text mb-4 text-center" style="font-size: 1em;">Gabinete do Deputado <?php echo $config['deputado']['nome'] ?></p>
        <p class="card-text mb-1 mt-2 text-center" style="font-size: 1.4em;"><b>Lista simples de proposições <?php echo (empty($termo)) ? '(' . $ano . ')' : '(' . $termo . ')' ?></b></p>
        <p class="card-text mb-1 mt-o text-center" style="font-size: 1.1em;"><em>(<?php echo ($tipo == 'PL') ? 'Projetos de Lei' : 'Requerimentos' ?>) (<?php echo ($arquivada == 0) ? 'Em tramitação' : 'Arquivados' ?></em>)</p>
    </div>
</div>

<div class="card mb-2 border-0  no-break">
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                <thead>
                    <tr>
                        <th scope="col">Proposição</th>
                        <th scope="col">Ementa/Resumo</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $busca = $proposicaoController->proposicoesGabinete($itens, $pagina, $ordenarPor, $ordem, $tipo, $ano, $termo, $arquivada);

                    if ($busca['status'] == 'success') {
                        foreach ($busca['dados'] as $proposicao) {
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
                    } elseif (in_array($busca['status'], ['empty', 'error'])) {
                        echo '<tr><td colspan="6">' . $busca['message'] . '</td></tr>';
                    }

                    ?>

                </tbody>
            </table>
        </div>

    </div>
</div>