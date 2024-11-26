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

if ($buscaProposicao['status'] != 'success') {
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

                    <a class="btn btn-secondary btn-sm custom-nav card-description" href="?secao=imprimir-proposicoes&pagina=1&ano=<?php echo $ano ?>&tipo=<?php echo $tipo ?>&arquivada=<?php echo $arquivada ?>&termo=<?php echo $termo ?>" target="_blank" role="button"><i class="bi bi-printer-fill"></i> Imprimir</a>
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
                <div class="card-body p-2">
                    <h5 class="card-title mb-0"><?php echo $buscaProposicao['dados'][0]['proposicao_titulo'] ?>
                        <hr class="mt-2">
                    </h5>
                    <p class="card-text mb-2" style="font-size:1em"><?php echo $buscaProposicao['dados'][0]['proposicao_ementa'] ?>
                        <hr>
                    </p>

                    <p class="card-text mb-2" style="font-size:1em"><i class="bi bi-calendar3"></i> Data de apresentação: <?php echo date('d/m', strtotime($buscaProposicao['dados'][0]['proposicao_apresentacao'])) ?></p>


                    <?php
                    if (count($buscarAutores['dados']) > 1) {
                        echo '<hr>';
                        foreach ($buscarAutores['dados'] as $autor) {
                            echo '<p class="card-text mb-0" style="font-size:1em"><i class="bi bi-person-fill"></i> ' . $autor['proposicao_autor_nome'] . ' ' . $autor['proposicao_autor_partido'] . '/' . $autor['proposicao_autor_estado'] . '</p>';
                        }
                    }
                    ?>


                    <?php

                    if ($buscaPrincipal['status'] == 'success') {

                        //print_r($buscaPrincipal);

                        echo '<hr><p class="card-text mb-1" style="font-size:1em"><i class="bi bi-arrow-right-short"></i> Proposição ao qual foi apensada: </p>';
                        echo '<p class="card-text mb-0 ms-4" style="font-size:1em"><i class="bi bi-dot"></i> <b><a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao?idProposicao=' . $buscaPrincipal['primeiroResultado']['id'] . '" target="_blank">' . $buscaPrincipal['primeiroResultado']['siglaTipo'] . ' ' . $buscaPrincipal['primeiroResultado']['numero'] . '/' . $buscaPrincipal['primeiroResultado']['ano'] . '</a></b></p>';
                        echo '<p class="card-text mb-2 ms-4"><i class="bi bi-dot"></i> <a href="' . $buscaPrincipal['primeiroResultado']['urlInteiroTeor'] . '" target="_blank">Inteiro teor <i class="bi bi-file-earmark-text"></i></a> </p>';

                        echo '<hr><p class="card-text mb-1" style="font-size:1em"><i class="bi bi-arrow-right-short"></i> Proposição principal: </p>';
                        echo '<p class="card-text mb-0 ms-4" style="font-size:1em"><i class="bi bi-dot"></i> <b><a href="https://www.camara.leg.br/proposicoesWeb/fichadetramitacao?idProposicao=' . $buscaPrincipal['ultimoResultado']['id'] . '" target="_blank">' . $buscaPrincipal['ultimoResultado']['siglaTipo'] . ' ' . $buscaPrincipal['ultimoResultado']['numero'] . '/' . $buscaPrincipal['ultimoResultado']['ano'] . '</a></b></p>';
                        echo '<p class="card-text mb-2 ms-4"><i class="bi bi-dot"></i> <a href="' . $buscaPrincipal['ultimoResultado']['urlInteiroTeor'] . '" target="_blank">Inteiro teor <i class="bi bi-file-earmark-text"></i></a> </p>';

                    }
                    ?>


                </div>
            </div>
        </div>
    </div>
</div>