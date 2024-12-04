<?php

use GabineteDigital\Controllers\ProposicaoController;
use GabineteDigital\Controllers\ReunioesController;

include '../src/Views/includes/verificaLogado.php';
require_once '../autoloader.php';

$config = require '../src/Configs/config.php';

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
                    <div class="accordion" id="accordionPanelsStayOpenExample">
                        <?php
                        foreach ($buscaPauta['dados'] as $proposicaoPauta) {

                            $buscaProposicaoDB = $proposicaoController->buscarProposicao('proposicao_titulo', preg_match('/^[^\/]+\/\d{4}/', $proposicaoPauta['titulo'], $matches) ? $matches[0] : '');
                            $buscaAutores = $proposicaoController->buscarAutores($buscaProposicaoDB['dados'][0]['proposicao_id']);
                            $buscaRelacionadas = $proposicaoController->buscarRelacionadas($buscaProposicaoDB['dados'][0]['proposicao_id']);
                            $flag = false;

                            foreach ($buscaAutores['dados'] as $autor) {
                                if ($autor['proposicao_autor_id'] == $config['deputado']['id']) {
                                    $flag = true;
                                } 
                            }

                            if (!empty($proposicaoPauta['relator']['nome']) && $proposicaoPauta['relator']['nome'] == $config['deputado']['id']) {
                                $flag = true;
                            }

                            $buscaAutoresRelacionada = [];
                            if ($buscaRelacionadas['status'] == 'success') {
                                foreach ($buscaRelacionadas['dados'] as $relacionada) {
                                    $autores = $proposicaoController->buscarAutores(basename($relacionada['uri']));
                                    if ($autores['status'] == 'success') {
                                        foreach ($autores['dados'] as $autor) {
                                            if ($autor['proposicao_autor_id'] == $config['deputado']['id']) {
                                                $flag = true;
                                                $buscaAutoresRelacionada[] = [
                                                    'proposicao' => $relacionada,
                                                    'autor' => $autor
                                                ];
                                            }
                                        }
                                    }
                                }
                            }

                            $titulo = $proposicaoPauta['ordem'] . ' | ' . $proposicaoPauta['titulo'];
                            $ementa = $buscaProposicaoDB['dados'][0]['proposicao_ementa'];

                        ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed <?= ($flag) ? 'bg-success text-white' : '' ?>" style="font-size: 13px;" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?= $proposicaoPauta['ordem'] ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?= $proposicaoPauta['ordem'] ?>">
                                        <?= $titulo ?>
                                    </button>
                                </h2>
                                <div id="panelsStayOpen-collapse<?= $proposicaoPauta['ordem'] ?>" class="accordion-collapse collapse">
                                    <div class="accordion-body">

                                        <p class="card-text mb-2"><em><?= $ementa ?></em></p>
                                        <hr class="mt-0">

                                        <p class="card-text mb-0"><b><i class="bi bi-dot"></i> Autoria: </b></p>

                                        <?php
                                        foreach ($buscaAutores['dados'] as $autor) {
                                            if ($autor['proposicao_autor_id'] == $config['deputado']['id']) {
                                                echo '<p class="card-text mb-2"><i class="bi bi-dot"></i> ' . $autor['proposicao_autor_nome'] . ' - ' . (!empty($autor['proposicao_autor_partido']) ? $autor['proposicao_autor_partido'] . '/' . $autor['proposicao_autor_estado'] : '') . '</p>';
                                            } else if ($autor['proposicao_autor_proponente'] == 1 && $autor['proposicao_autor_assinatura'] == 1) {
                                                echo '<p class="card-text mb-2"><i class="bi bi-dot"></i> ' . $autor['proposicao_autor_nome'] . ' - ' . (!empty($autor['proposicao_autor_partido']) ? $autor['proposicao_autor_partido'] . '/' . $autor['proposicao_autor_estado'] : '') . '</p>';
                                            }
                                        }
                                        ?>

                                        <?php
                                        if (!empty($proposicaoPauta['relator']['nome'])) {
                                            echo ' <hr><p class="card-text mb-0"><b><i class="bi bi-dot"></i> Relatoria: </b></p>';
                                            echo '<i class="bi bi-dot"></i> ' . $proposicaoPauta['relator']['nome'] . ' - ' . $proposicaoPauta['relator']['siglaPartido'] . '/' . $proposicaoPauta['relator']['siglaUf'];
                                        }
                                        ?>

                                        <?php
                                        if (!empty($proposicaoPauta['textoParecer'])) {
                                            echo '<p class="card-text mb-0 mt-2"><b><i class="bi bi-dot"></i> Parecer do relator: </b></p>';
                                            echo '<p class="card-text mb-0"><i class="bi bi-dot"></i> ' . $proposicaoPauta['textoParecer'] . ' </p><hr>';
                                        }
                                        ?>

                                        <?php
                                        if (!empty($buscaAutoresRelacionada)) {
                                            echo '<p class="card-text mb-0 mt-2"><b><i class="bi bi-dot"></i> Apensados do deputado '.$config['deputado']['nome'].': </b></p>';
                                            echo '<p class="card-text mb-0"><i class="bi bi-dot"></i> ' . $buscaAutoresRelacionada[0]['proposicao']['siglaTipo'] . ' ' . $buscaAutoresRelacionada[0]['proposicao']['numero'] . '/' . $buscaAutoresRelacionada[0]['proposicao']['ano'] . '</p>';
                                            echo '<p class="card-text mb-0"><i class="bi bi-dot"></i> <em>' . $buscaAutoresRelacionada[0]['proposicao']['ementa'] . '</em></p>';
                                        }
                                        ?>

                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>

                </div>
            </div>


        </div>
    </div>
</div>