<?php

use GabineteDigital\Controllers\ProposicaoController;
use GabineteDigital\Controllers\ReunioesController;

include '../src/Views/includes/verificaLogado.php';
require_once '../autoloader.php';

$reunioesController = new ReunioesController();
$proposicaoController = new ProposicaoController();


$reuniaoId = $_GET['reuniao'];

$buscaPauta = $reunioesController->buscarPauta($reuniaoId);

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
                    
                </div>
            </div>


        </div>
    </div>
</div>