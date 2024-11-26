<?php

include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';
$config = require '../src/Configs/config.php';

use GabineteDigital\Controllers\ProposicaoController;

$proposicaoController = new ProposicaoController();

$id = $_GET['id'];

$buscaProposicao = $proposicaoController->buscarProposicao($id);
$buscarAutores = $proposicaoController->buscarAutores($id);
$buscaPrincipal = $proposicaoController->buscarUltimaProposicao($id);
$buscaTramitacoes = $proposicaoController->buscarTramitacoes($id);

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
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=proposicoes" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
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
                    <h5 class="card-title mb-0"><?php echo $buscaProposicao['dados']['siglaTipo'] . ' ' . $buscaProposicao['dados']['numero'] . '/' . $buscaProposicao['dados']['ano'] ?><?php echo ($buscaProposicao['dados']['statusProposicao']['descricaoSituacao'] == 'Arquivada') ? ' | <small>Arquivado</small>' : '' ?>
                        <hr class="mt-2">
                    </h5>
                    <p class="card-text mb-2" style="font-size:1em"><?php echo $buscaProposicao['dados']['ementa']  ?>
                        <hr>
                    </p>

                    <p class="card-text mb-0" style="font-size:1em"><i class="bi bi-dot"></i> Data de apresentação: <?php echo date('d/m', strtotime($buscaProposicao['dados']['dataApresentacao'])) ?></p>
                    <p class="card-text mb-0" style="font-size:1em"><a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao/?idProposicao=<?php echo $buscaProposicao['dados']['id']  ?>" target="_blank"><i class="bi bi-dot"></i> Ver página da Câmara</a></p>
                    <p class="card-text mb-2" style="font-size:1em"><a href="<?php echo $buscaProposicao['dados']['urlInteiroTeor']  ?>" target="_blank"><i class="bi bi-dot"></i> Ver inteiro teor</a></p>

                    <?php

                    if (count($buscarAutores['dados']) > 1) {
                        echo '<hr><b><i class="bi bi-people-fill"></i> Autores</b>';
                        foreach ($buscarAutores['dados'] as $autor) {
                            echo '<p class="card-text mb-0" style="font-size:1em"> <i class="bi bi-dot"></i> ' . $autor['proposicao_autor_nome'] . ' ' . $autor['proposicao_autor_partido'] . '/' . $autor['proposicao_autor_estado'] . '</p>';
                        }
                    }

                    ?>

                    <?php
                    if ($buscaPrincipal['status'] == 'success') {
                        if ($buscaPrincipal['dados']['id'] != $id) {
                            echo '<hr><p class="card-text mb-1" style="font-size:1em"><i class="bi bi-arrow-right-short"></i> Proposição principal: </p>';
                            echo '<p class="card-text mb-0 ms-3" style="font-size:1em"><i class="bi bi-dot"></i> <b>' . $buscaPrincipal['dados']['siglaTipo'] . ' ' . $buscaPrincipal['dados']['numero'] . '/' . $buscaPrincipal['dados']['ano'] . '</b></p>';
                            echo '<p class="card-text mb-0 ms-3"><i class="bi bi-dot"></i> <a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao?idProposicao=' . $buscaPrincipal['dados']['id'] . '" target="_blank">Ver página da Câmara</a> </p>';
                            echo '<p class="card-text mb-2 ms-3" style="font-size:1em"><a href="' . $buscaPrincipal['dados']['urlInteiroTeor'] . '" target="_blank"><i class="bi bi-dot"></i> Ver inteiro teor</a></p>';
                        } else {
                            echo '<hr><p class="card-text mb-1" style="font-size:1em"><i class="bi bi-arrow-right-short"></i> Proposição principal: </p>';
                            echo '<p class="card-text mb-0 ms-3" style="font-size:1em"><i class="bi bi-dot"></i> <b>Essa proposição não foi apensada</b></p>';
                        }
                    }
                    ?>

                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-header bg-success text-white px-2 py-1">Tramitações</div>
                <div class="card-body p-1">

                </div>
            </div>
        </div>
    </div>
</div>