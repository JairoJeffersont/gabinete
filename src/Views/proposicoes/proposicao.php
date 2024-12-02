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
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=proposicoes" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                    <?php
                        if($notas['status'] == 'success'){
                            echo '<a class="btn btn-secondary btn-sm custom-nav card-description" href="?secao=imprimir-ficha-proposicao&id='.$id.'" target="_blank" role="button"><i class="bi bi-printer-fill"></i> Imprimir</a>';
                        }else{
                            echo '<a class="btn btn-secondary btn-sm custom-nav card-description disabled" href="#" target="_blank" role="button"><i class="bi bi-printer-fill"></i> Imprimir</a>';
                        }
                    ?>
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
                        echo '<p class="card-text mb-2 mt-3" style="font-size:1.1em"><b>' . $notas['dados'][0]['nota_titulo'] . '</b></p>';
                        echo '<p class="card-text mb-3" style="font-size:1em">' . $notas['dados'][0]['nota_resumo'] . '</p>';
                    }
                    ?>

                    <em>
                        <p class="card-text mt-2" style="font-size:1em"><?php echo $buscaProposicao['dados'][0]['proposicao_ementa']  ?>
                    </em>

                </div>
            </div>

            <div class="card mb-2 card-description ">
                <ul class="list-group">
                    <li class="list-group-item"><i class="bi bi-calendar3"></i> Data de apresentação: <?php echo date('d/m/Y - H:i', strtotime($buscaProposicao['dados'][0]['proposicao_apresentacao']))  ?></li>
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
            <div class="card shadow-sm mb-2 card-description">
                <div class="card-header bg-success text-white px-2 py-1">Criar nota técnica</div>
                <div class="card-body p-2">


                    <?php

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'nota_proposicao' => $id,
                            'nota_titulo' => htmlspecialchars($_POST['nota_titulo'], ENT_QUOTES, 'UTF-8'),
                            'nota_resumo' => htmlspecialchars($_POST['nota_resumo'], ENT_QUOTES, 'UTF-8'),
                            'nota_texto' => htmlspecialchars($_POST['nota_texto'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $notaController->criarNotaTecnica($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                                    setTimeout(function(){
                                        window.location.href = "?secao=proposicao&id=' . $id . '";
                                    }, 300);</script>';
                        } else if ($result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }


                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {
                        $dados = [
                            'nota_proposicao' => $id,
                            'nota_titulo' => htmlspecialchars($_POST['nota_titulo'], ENT_QUOTES, 'UTF-8'),
                            'nota_resumo' => htmlspecialchars($_POST['nota_resumo'], ENT_QUOTES, 'UTF-8'),
                            'nota_texto' => htmlspecialchars($_POST['nota_texto'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $notaController->atualizarNotaTecnica($id, $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                            setTimeout(function(){
                                 window.location.href = "?secao=proposicao&id=' . $id . '";
                            }, 300);</script>';
                        } else if ($result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $notaController->apagarNotaTecnica($id);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                                setTimeout(function(){
                                   window.location.href = "?secao=proposicao&id=' . $id . '";
                                }, 300);</script>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    ?>

                    <form class="row g-2 form_custom" method="POST">
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control" name="nota_titulo" placeholder="Título" value="<?php echo ($notas['status'] == 'success') ? $notas['dados'][0]['nota_titulo'] : '' ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control" name="nota_resumo" placeholder="Resumo" value="<?php echo ($notas['status'] == 'success') ? $notas['dados'][0]['nota_resumo'] : '' ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control" disabled value="<?php echo ($notas['status'] == 'success') ? $notas['dados'][0]['usuario_nome'] : '' ?>" required>
                        </div>
                        <div class="col-md-12 col-12">
                            <script>
                                tinymce.init({
                                    selector: 'textarea',
                                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount fullscreen',
                                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | alignleft aligncenter alignright alignjustify | numlist bullist indent outdent | emoticons charmap | removeformat | fullscreen',
                                    height: 400,
                                    language: 'pt_BR',
                                    content_css: "public/css/tinymce.css",
                                    setup: function(editor) {
                                        editor.on('init', function() {
                                            editor.getBody().style.fontSize = '10pt';
                                        });
                                    }
                                });
                            </script>
                            <textarea class="form-control form-control-sm" name="nota_texto" placeholder="Texto" rows="10"><?php echo ($notas['status'] == 'success') ? $notas['dados'][0]['nota_texto'] : '' ?></textarea>
                        </div>
                        <div class="col-md-6 col-12">
                            <?php
                            if ($notas['status'] == 'not_found' || is_integer($id) || $notas['status'] == 'error') {
                                echo '<button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>';
                            } else {
                                echo '<button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>&nbsp;';
                                echo '<button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-floppy-fill"></i> Apagar</button>';
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>