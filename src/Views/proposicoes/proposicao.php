<?php

include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';
$config = require '../src/Configs/config.php';

use GabineteDigital\Controllers\NotaTecnicaController;
use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();

$id = $_GET['id'];

$buscaProposicao = $proposicaoController->buscarProposicao($id);
$buscaPrincipal = $proposicaoController->buscarUltimaProposicao($id);
$buscaTramitacoes = $proposicaoController->buscarTramitacoes($id);

$notas = $notaController->buscarNotaTecnica('nota_proposicao', $id);


if (empty($buscaProposicao['dados'])) {
    header('Location: ?secao=proposicoes');
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
                    <a class="btn btn-secondary btn-sm custom-nav card-description" href="?secao=imprimir-ficha-proposicoes&id=<?php echo $id ?>" target="_blank" role="button"><i class="bi bi-printer-fill"></i> Imprimir</a>
                </div>
            </div>
            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text-fill"></i> Ficha do proposição</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Informações da proposição</p>
                </div>
            </div>

            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1">Detalhes</div>
                <div class="card-body p-3">
                    <h5 class="card-title mb-0">
                        <a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao/?idProposicao=<?php echo $buscaProposicao['dados'][0]['proposicao_id'] ?>" target="_blank"><?php echo $buscaProposicao['dados'][0]['proposicao_titulo'] ?></a><?php echo ($buscaProposicao['dados'][0]['proposicao_arquivada']) ? ' | <small>Arquivado</small>' : ' | <small>Em tramitação</small>' ?></a>
                    </h5>

                    <?php
                    if ($notas['status'] == 'success') {
                        echo '<p class="card-text mb-0 mt-2" style="font-size:1.1em"><b>' . $notas['dados'][0]['nota_titulo'] . '</b></p>';
                        echo '<p class="card-text mb-3" style="font-size:1em">' . $notas['dados'][0]['nota_resumo'] . '</p>';
                    }
                    ?>

                    <em><p class="card-text mt-2" style="font-size:1em"><?php echo $buscaProposicao['dados'][0]['proposicao_ementa']  ?></em>

                </div>
            </div>

            <div class="card mb-2 card-description ">
                <ul class="list-group">
                    <li class="list-group-item"><i class="bi bi-calendar3"></i> Data de apresentação: <?php echo date('d/m/Y - H:i', strtotime($buscaProposicao['dados'][0]['proposicao_apresentacao']))  ?></li>
                    <!--<li class="list-group-item"><a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao/?idProposicao=<?php echo $buscaProposicao['dados'][0]['proposicao_id'] ?>" target="_blank"><i class="bi bi-globe2"></i> Ir para página da Câmara</a></li>-->
                    <?php

                    if ($buscaProposicao['dados'][0]['proposicao_principal']) {
                        echo '<li class="list-group-item"><i class="bi bi-info-circle"></i> Proposição ao qual foi apensada: <a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao/?idProposicao=' . $buscaProposicao['dados'][0]['proposicao_principal'] . '" target="_blank">' . $buscaProposicao['dados'][0]['proposicao_principal_titulo'] . '</a></li>';
                    }

                    if ($buscaPrincipal['status'] == 'success' && $buscaPrincipal['dados']['id'] != $buscaProposicao['dados'][0]['proposicao_id'] && $buscaProposicao['dados'][0]['proposicao_principal'] !== $buscaPrincipal['dados']['id']) {
                        echo ' <li class="list-group-item"><i class="bi bi-info-circle"></i> Proposição principal: <a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao/?idProposicao=' . $buscaPrincipal['dados']['id'] . '" target="_blank">' . $buscaPrincipal['dados']['siglaTipo'] . ' ' . $buscaPrincipal['dados']['numero'] . '/' . $buscaPrincipal['dados']['ano'] . '</a></li>';
                    }

                    ?>
                </ul>
            </div>

            <div class="card shadow-sm mb-2 card-description">
                <div class="card-header bg-secondary text-white px-2 py-1">Últimas tramitações</div>

                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-2 ">
                            <thead>
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Despacho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                if (isset($buscaTramitacoes['dados']) && !empty($buscaTramitacoes['dados'])) {

                                    usort($buscaTramitacoes['dados'], function ($a, $b) {
                                        return $b['sequencia'] - $a['sequencia'];
                                    });

                                    foreach (array_slice($buscaTramitacoes['dados'], 0, 10) as $tramitacoes) {
                                        echo '<tr>';
                                        echo '<td>' . date('d/m/y', strtotime($tramitacoes['dataHora'])) . '</td>';
                                        echo '<td>' . (!empty($tramitacoes['url'])
                                            ? '<a href="' . $tramitacoes['url'] . '" target="_blank">' . $tramitacoes['siglaOrgao'] . ' | ' . mb_strimwidth($tramitacoes['despacho'], 0, 100, '...') . ' <i class="bi bi-box-arrow-up-right"></i></a>'
                                            : $tramitacoes['siglaOrgao'] . ' | ' . $tramitacoes['despacho']) . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6">Erro interno do servidor</td></tr>';
                                }

                                ?>
                            </tbody>
                        </table>
                        <small>* Essas informações são extraídas da base de dados da CD.</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>