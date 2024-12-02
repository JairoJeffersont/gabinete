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

if (empty($notas['dados'])) {
    echo '<script> window.close();</script>';
    exit();
}
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
        <p class="card-text mb-2 mt-4 text-center" style="font-size: 1.4em;"><b><?php echo $buscaProposicao['dados'][0]['proposicao_titulo'] ?></b> </p>
        <p class="card-text mb-2 text-center" style="font-size: 1.2em;"><b><?php echo $notas['dados'][0]['nota_titulo'] ?></b></p>
        <p class="card-text mb-4 text-center style=" font-size: 1.2em;"><?php echo $notas['dados'][0]['nota_resumo'] ?></p>
        <p class="card-text mb-0 text-center" style="font-size: 1.3em;"><b>Nota técnica</b></p>
        <p class="card-text mb-4 text-center" style="font-size: 0.8em;">criada por (<?php echo $notas['dados'][0]['usuario_nome'] ?>)</p>
        <p class="card-text">
        <div id="nota_texto_print"><?php echo htmlspecialchars_decode($notas['dados'][0]['nota_texto']); ?></p>
        </div>



    </div>
</div>