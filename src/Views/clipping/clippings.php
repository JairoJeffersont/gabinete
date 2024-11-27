<?php

include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';

use GabineteDigital\Controllers\ClippingController;
use GabineteDigital\Controllers\ClippingTipoController;
use GabineteDigital\Controllers\OrgaoController;

$clippingTipoController = new ClippingTipoController;
$clippingController = new ClippingController;
$orgaoController = new OrgaoController;
?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?pagina=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2 card-description">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-file-earmark-text-fill"></i>Clipping</div>
                <div class="card-body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar os clipping, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $clipping = [
                            'clipping_resumo' => $_POST['clipping_resumo'],
                            'clipping_titulo' => $_POST['clipping_titulo'],
                            'clipping_link' => $_POST['clipping_link'],
                            'clipping_orgao' => $_POST['clipping_orgao'],
                            'arquivo' => $_FILES['clipping_arquivo'],
                            'clipping_tipo' => $_POST['clipping_tipo']
                        ];

                        $result = $clippingController->criarClipping($clipping);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">

                        <div class="col-md-3 col-12">
                            <input type="url" class="form-control form-control-sm" name="clipping_link" placeholder="Link (http://...)" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="clipping_titulo" placeholder="Titulo" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="clipping_orgao" id="clipping_orgao" required>
                                <option value="1000">Veículo não informado</option>
                                <?php
                                $buscaOrgaos = $orgaoController->listarOrgaos(1000, 1, 'asc', 'orgao_nome', '', false);
                                if ($buscaOrgaos['status'] == 'success') {
                                    foreach ($buscaOrgaos['dados'] as $orgaos) {
                                        if ($orgaos['orgao_id'] == 1000) {
                                            echo '<option value="' . $orgaos['orgao_id'] . '" selected>' . $orgaos['orgao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgaos['orgao_id'] . '">' . $orgaos['orgao_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Novo órgão + </option>
                            </select>
                        </div>

                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="clipping_tipo" id="clipping_tipo" required>
                                <?php
                                $buscaTipos = $clippingTipoController->listarClippingTipos();
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['dados'] as $tipos) {
                                        if ($tipos['clipping_tipo_id'] == 1000) {
                                            echo '<option value="' . $tipos['clipping_tipo_id'] . '" selected>' . $tipos['clipping_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipos['clipping_tipo_id'] . '">' . $tipos['clipping_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="file" class="form-control form-control-sm" name="clipping_arquivo" placeholder="Arquivo">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="clipping_resumo" rows="10" placeholder="Texto do clipping" required></textarea>
                        </div>
                        <div class="col-md-2 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Título</th>
                                    <th scope="col">Veículo</th>
                                    <th scope="col">Criado por - em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $busca = $clippingController->listarClippings();
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $clipping) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=clipping&id=' . $clipping['clipping_id'] . '">' . $clipping['clipping_titulo'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $clipping['orgao_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $clipping['usuario_nome'] . ' - ' . date('d/m', strtotime($clipping['clipping_criado_por'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="4">Erro ao carregar os dados.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#clipping_orgao').change(function() {
        if ($('#clipping_orgao').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo órgão?")) {
                window.location.href = "?secao=orgaos";
            } else {
                $('#orgao').val(1000).change();
            }
        }
    });
</script>