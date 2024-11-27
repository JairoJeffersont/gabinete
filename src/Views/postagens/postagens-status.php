<?php


include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';

use GabineteDigital\Controllers\PostagemStatusController;

$postagensStatus = new PostagemStatusController();

?>

<div class="d-flex" id="wrapper">
    <?php include '../src/Views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include '../src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>

            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-people-fill"></i> Adicionar usuários</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Todos os campos são obrigatórios (exceto a foto) <br> A foto deve ser em JPG ou PNG</p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'postagem_status_nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
                            'postagem_status_descricao' => htmlspecialchars($_POST['descricao'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $postagensStatus->criarPostagemStatus($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>


                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-12 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome do status" required>
                        </div>

                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="descricao" rows="5" placeholder="Descrição do status de postagem"></textarea>
                        </div>
                        <div class="col-md-4 col-6">
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
                                    <th scope="col">Status</th>
                                    <th scope="col">Descrição</th>
                                    <th scope="col">Criado por - em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $busca = $postagensStatus->listarPostagemStatus();
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $status) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=postagem-status&id=' . $status['postagem_status_id'] . '">' . $status['postagem_status_nome'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $status['postagem_status_descricao'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $status['usuario_nome'] . ' - ' . date('d/m', strtotime($status['postagem_status_criado_em'])) . '</td>';
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