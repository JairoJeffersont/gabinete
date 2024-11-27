<?php


include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';

use GabineteDigital\Controllers\PostagemController;
use GabineteDigital\Controllers\PostagemStatusController;

$postagensStatus = new PostagemStatusController();
$postagens = new PostagemController();

$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$termo = isset($_GET['termo']) ? $_GET['termo'] : '';
$statusGet = isset($_GET['status']) ? $_GET['status'] : 0;

$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['postagem_criada_em']) ? htmlspecialchars($_GET['ordenarPor']) : 'postagem_criada_em';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'asc';


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
                <div class="card-header bg-primary text-white px-2 py-1 card-background">Adicionar postagens</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Seção para gerenciamento de postagens
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $postagemDados = [
                            'postagem_titulo' => $_POST['postagem_titulo'],
                            'postagem_data' => $_POST['postagem_data'],
                            'postagem_informacoes' => $_POST['postagem_informacoes'],
                            'postagem_status' => $_POST['postagem_status'],
                            'postagem_midias' => $_POST['postagem_midias']
                        ];

                        $result = $postagens->criarPostagem($postagemDados);

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
                            <input type="text" class="form-control form-control-sm" name="postagem_titulo" placeholder="Título (Post dia das crianças, Aniversário do deputado...)" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_midias" placeholder="Mídias (facebook, instagram, site...)" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="date" class="form-control form-control-sm" name="postagem_data" value="<?php echo date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <select class="form-select form-select-sm" name="postagem_status" required>
                                <?php
                                $status_postagens = $postagensStatus->listarPostagemStatus();
                                if ($status_postagens['status'] == 'success') {
                                    foreach ($status_postagens['dados'] as $status) {
                                        if ($status['postagem_status_id'] == 1000) {
                                            echo '<option value="' . $status['postagem_status_id'] . '" selected>' . $status['postagem_status_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $status['postagem_status_id'] . '">' . $status['postagem_status_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="postagem_informacoes" placeholder="Informações, texto da postagem, legendas...." rows="6" required></textarea>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <input type="hidden" name="secao" value="postagens" />
                                <div class="col-md-1 col-12">
                                    <input type="text" class="form-control form-control-sm" name="ano" value="<?php echo $ano ?>">
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="ordem" required>
                                        <option value="asc" <?php echo $ordem == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                                        <option value="desc" <?php echo $ordem == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="itens" required>
                                        <option value="5" <?php echo $itens == 5 ? 'selected' : ''; ?>>5 itens</option>
                                        <option value="10" <?php echo $itens == 10 ? 'selected' : ''; ?>>10 itens</option>
                                        <option value="25" <?php echo $itens == 25 ? 'selected' : ''; ?>>25 itens</option>
                                        <option value="50" <?php echo $itens == 50 ? 'selected' : ''; ?>>50 itens</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12">
                                    <select class="form-select form-select-sm" name="status" required>
                                        <option value="0" <?php echo $statusGet == 0 ? 'selected' : ''; ?>>Todas</option>
                                        <?php
                                        $status_postagens = $postagensStatus->listarPostagemStatus();
                                        if ($status_postagens['status'] == 'success') {
                                            foreach ($status_postagens['dados'] as $status) {
                                                if ($status['postagem_status_id'] == $statusGet) {
                                                    echo '<option value="' . $status['postagem_status_id'] . '" selected>' . $status['postagem_status_nome'] . '</option>';
                                                } else {
                                                    echo '<option value="' . $status['postagem_status_id'] . '">' . $status['postagem_status_nome'] . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-10">
                                    <input type="text" class="form-control form-control-sm" name="termo" placeholder="Buscar..." value="<?php echo $termo ?>">
                                </div>
                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Titulo</th>
                                    <th scope="col">Mídias</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Criado por | em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php



                                $busca = $postagens->listarPostagens($ano, $itens, $pagina, $ordem, $ordenarPor, $statusGet, $termo);
                                //print_r($busca['total_paginas']);

                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $postagem) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=postagem&id=' . $postagem['postagem_id'] . '">' . $postagem['postagem_titulo'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['postagem_midias'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['postagem_status_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['usuario_nome'] . ' | ' . date('d/m', strtotime($postagem['postagem_criada_por'])) . '</td>';
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
                    <?php
                    if (isset($busca['total_paginas'])) {
                        $totalPagina = $busca['total_paginas'];
                    } else {
                        $totalPagina = 0;
                    }

                    if ($totalPagina > 0 && $totalPagina != 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                        echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=postagens&itens=' . $itens . '&pagina=1&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">Primeira</a></li>';

                        for ($i = 1; $i < $totalPagina - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=postagens&itens=' . $itens . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">' . $pageNumber . '</a></li>';
                        }

                        echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=postagens&itens=' . $itens . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">Última</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</div>