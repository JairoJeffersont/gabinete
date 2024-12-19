<?php


require_once './vendor/autoload.php';

use GabineteDigital\Controllers\ClienteController;
use GabineteDigital\Controllers\UsuarioController;
use GabineteDigital\Middleware\GetJson;

$usuarioController = new UsuarioController();
$clienteController = new ClienteController();
$getJson = new GetJson();

?>
<link href="public/css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">

        <img src="public/img/logo_white.png" alt="" class="img_logo" />
        <h2 class="login_title mb-1">Gabinete Digital</h2>
        <h6 class="host mb-3">Novo Cliente</h6>

        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
            $cliente = [
                'cliente_nome' => htmlspecialchars($_POST['cliente_nome'], ENT_QUOTES, 'UTF-8'),
                'cliente_email' => htmlspecialchars($_POST['cliente_email'], ENT_QUOTES, 'UTF-8'),
                'cliente_telefone' => preg_replace('/[^0-9]/', '', $_POST['cliente_telefone']),
                'cliente_cpf_cnpj' => preg_replace('/[^0-9]/', '', $_POST['cliente_cpf_cnpj']),
                'cliente_endereco' => htmlspecialchars($_POST['cliente_endereco'], ENT_QUOTES, 'UTF-8'),
                'cliente_cep' => preg_replace('/[^0-9]/', '', $_POST['cliente_cep']),
                'cliente_assinaturas' => preg_replace('/[^0-9]/', '', $_POST['cliente_assinaturas']),
                'cliente_ativo' => 1,
                'cliente_deputado_nome' => htmlspecialchars($_POST['cliente_deputado_nome'], ENT_QUOTES, 'UTF-8'),
                'cliente_deputado_id' => htmlspecialchars($_POST['cliente_deputado_id'], ENT_QUOTES, 'UTF-8'),
                'cliente_deputado_estado' => htmlspecialchars($_POST['cliente_deputado_estado'], ENT_QUOTES, 'UTF-8'),
            ];

            $result = $clienteController->criarCliente($cliente);

            if ($result['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="3" role="alert">' . $result['message'] . '. <br>Você receberá um email com instruções de acesso</div>';
            } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="0" role="alert">' . $result['message'] . '</div>';
            } else if ($result['status'] == 'error') {
                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert rounded-5" data-timeout="0" role="alert">' . $result['message'] . ' | Código do erro: ' . $result['id_erro'] . '</div>';
            }
        }

        ?>
        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="cliente_nome" placeholder="Nome" required>
            </div>
            <div class="col-md-12 col-12">
                <input type="email" class="form-control form-control-sm" name="cliente_email" placeholder="Email" required>
            </div>
            <div class="col-md-6 col-12">
                <input type="text" class="form-control form-control-sm" name="cliente_endereco" placeholder="Endereço" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="cliente_cep" placeholder="CEP" data-mask="00000-000" required>
            </div>
            <div class="col-md-12 col-6">
                <input type="text" class="form-control form-control-sm" name="cliente_cpf_cnpj" placeholder="CPF" data-mask="000.000.000-00" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="cliente_telefone" placeholder="Telefone (com DDD)" data-mask="(00) 00000-0000" maxlength="15" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="cliente_assinaturas" placeholder="Licenças" data-mask="00">
            </div>
            <div class="col-md-12 col-12">
                <select class="form-select form-select-sm form_dep" id="usuario_nivel" name="usuario_nivel" required>
                    <option value="" disabled selected>Escolha o deputado do Gabinete</option>
                    <?php
                    $buscaDeps = $getJson->getJson('https://dadosabertos.camara.leg.br/api/v2/deputados?idLegislatura=57&itens=1000&ordem=ASC&ordenarPor=nome');
                    foreach ($buscaDeps['dados'] as $dep) {
                        echo '<option value="' . $dep['id'] . '" data-nome="' . $dep['nome'] . '" data-siglauf="' . $dep['siglaUf'] . '">'
                            . $dep['nome'] . '/' . $dep['siglaUf'] . '</option>';
                    }
                    ?>
                </select>

            </div>
            <input type="hidden" name="cliente_deputado_id" id="dep_id">
            <input type="hidden" name="cliente_deputado_nome" id="dep_nome">
            <input type="hidden" name="cliente_deputado_estado" id="dep_siglauf">
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_salvar" class="btn btn-primary">Salvar</button>
                <a type="button" href="?secao=login" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
        <p class="mt-3 copyright">2024 | JS Digital System</p>
    </div>
</div>

<script>
    document.getElementById('form_novo').addEventListener('submit', function(e) {
        const select = document.getElementById('usuario_nivel');
        const selectedOption = select.options[select.selectedIndex];

        const depId = selectedOption.value;
        const depNome = selectedOption.getAttribute('data-nome');
        const depSiglaUf = selectedOption.getAttribute('data-siglauf');

        document.getElementById('dep_id').value = depId;
        document.getElementById('dep_nome').value = depNome;
        document.getElementById('dep_siglauf').value = depSiglaUf;
    });
</script>