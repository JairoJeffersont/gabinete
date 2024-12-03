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
                    <div class="accordion" id="accordionPanelsStayOpenExample">
                        <?php

                        if ($buscaPauta['status'] == 'empty' || $buscaPauta['status'] == 'error') {
                            echo ' <p class="card-text mb-0">Reunião sem pauta</p>';
                        }

                        foreach ($buscaPauta['dados'] as $proposicaoPauta) {

                            $buscaProposicaoDB = $proposicaoController->buscarProposicao('proposicao_titulo', preg_match('/^[^\/]+\/\d{4}/', $proposicaoPauta['titulo'], $matches) ? $matches[0] : '');
                            $buscarAutores = $proposicaoController->buscarAutores($buscaProposicaoDB['dados'][0]['proposicao_id'])['dados'];

                            $id = $buscaProposicaoDB['dados'][0]['proposicao_id'];
                            $titulo = $proposicaoPauta['ordem'] . ' - ' . $proposicaoPauta['titulo'];
                            $ementa = $buscaProposicaoDB['dados'][0]['proposicao_ementa'];
                            $regime = $proposicaoPauta['regime'];


                            if (isset($proposicaoPauta['relator']['nome'])) {
                                $relator = $proposicaoPauta['relator']['nome'] . ' - ' . $proposicaoPauta['relator']['siglaPartido'] . '/' . $proposicaoPauta['relator']['siglaUf'];
                            }


                            echo '<div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" style="font-size:13px" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $id . '" aria-expanded="false" aria-controls="panelsStayOpen-collapse' . $id . '">
                                        ' . $titulo . '
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapse' . $id . '" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <p class="card-text"><em>' . $ementa . '</em></p><hr>
                                            <p class="card-text mb-1">Autoria:</p>';

                                            foreach ($buscarAutores as $autor) {
                                                if ($autor['proposicao_autor_proponente'] == 1 && $autor['proposicao_autor_assinatura'] == 1) {
                                                    echo '<p class="card-text mb-0"><i class="bi bi-person"></i> <b>' . $autor['proposicao_autor_nome'] . ' - ' . $autor['proposicao_autor_partido'] . '/' . $autor['proposicao_autor_estado'] . '</b></p>';
                                                }
                                            }

                                            echo '<hr>';

                                            if (isset($proposicaoPauta['relator']['nome'])) {
                                                echo '<p class="mb-1 mt-0"><i class="bi bi-person"></i> Relator(a): <b>' . $proposicaoPauta['relator']['nome'] . ' - ' . $proposicaoPauta['relator']['siglaPartido'] . '/' . $proposicaoPauta['relator']['siglaUf'] . '</b></p>';
                                            }

                                            if (isset($proposicaoPauta['textoParecer'])) {
                                                echo '<p class="mb-0"><i class="bi bi-file-earmark-text"></i> ' . $proposicaoPauta['textoParecer'] . '</p>';
                                            }

                            echo '</div>
                                    </div>
                                </div>';
                        }


                        ?>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>