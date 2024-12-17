<?php

use GabineteDigital\Controllers\ClienteController;
use GabineteDigital\Middleware\GetJson;

include './src/Middleware/verificaLogado.php';

if ($_SESSION['usuario_nivel'] != 0) {
    header('Location: ?secao=home');
}

require_once './vendor/autoload.php';

$clienteController = new ClienteController();
$getJson = new GetJson();

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/includes/sider_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/includes/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav card-description" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>

            <div class="card mb-2 card-description ">
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-people-fill"></i> Adicionar clientes</div>
                <div class="card-body p-2">
                    <p class="card-text mb-0">Seção para adicionar e gerenciar os clientes do sistema. Cada cliente poderá ter uma quantidade especifica de usuários</p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $cliente = [
                            'cliente_nome' => htmlspecialchars($_POST['cliente_nome'], ENT_QUOTES, 'UTF-8'),
                            'cliente_email' => htmlspecialchars($_POST['cliente_email'], ENT_QUOTES, 'UTF-8'),
                            'cliente_telefone' => htmlspecialchars($_POST['cliente_telefone'], ENT_QUOTES, 'UTF-8'),
                            'cliente_assinaturas' => htmlspecialchars($_POST['cliente_assinaturas'], ENT_QUOTES, 'UTF-8'),
                            'cliente_ativo' => htmlspecialchars($_POST['cliente_ativo'], ENT_QUOTES, 'UTF-8'),
                            'cliente_deputado_nome' => htmlspecialchars($_POST['cliente_deputado_nome'], ENT_QUOTES, 'UTF-8'),
                            'cliente_deputado_id' => htmlspecialchars($_POST['cliente_deputado_id'], ENT_QUOTES, 'UTF-8'),
                        ];

                        $result = $clienteController->criarCliente($cliente);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' | Código do erro: ' . $result['id_erro'] . '</div>';
                        }
                    }

                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="cliente_nome" placeholder="Nome do cliente" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="email" class="form-control form-control-sm" name="cliente_email" placeholder="Email do cliente" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="cliente_telefone" placeholder="Telefone do cliente" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="text" class="form-control form-control-sm" name="cliente_assinaturas" placeholder="Qtd de usuários" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="cliente_ativo" required>
                                <option value="1" selected>Ativado</option>
                                <option value="0">Desativado</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="cliente_deputado_nome" placeholder="Deputado do gabinete" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="cliente_deputado_id" placeholder="ID deputado do gabinete" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
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
                                    <th scope="col">Nome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Telefone</th>
                                    <th scope="col">Usuários</th>
                                    <th scope="col">Ativo</th>
                                    <th scope="col">Ativo</th>
                                    <th scope="col">Deputado</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $busca = $clienteController->listarClientes();
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $cliente) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap; justify-content: center; align-items: center;"><a href="?secao=cliente&id=' . $cliente['cliente_id'] . '">' . $cliente['cliente_nome'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $cliente['cliente_email'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $cliente['cliente_telefone'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $cliente['cliente_assinaturas'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . ($cliente['cliente_ativo'] ? 'Ativo' : 'Desativado') . '</td>';
                                        echo '<td style="white-space: nowrap;">' . date('m/d', strtotime($cliente['cliente_criado_em'])) . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $cliente['cliente_deputado_nome'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="7">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="7">' . $busca['message'] . ' | Código do erro: ' . $busca['id_erro'] . '</td></tr>';
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