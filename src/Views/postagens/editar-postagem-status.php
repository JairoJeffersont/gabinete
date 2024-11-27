<?php


include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';

use GabineteDigital\Controllers\PostagemStatusController;

$postagensStatus = new PostagemStatusController();

$id = $_GET['id'];

$busca = $postagensStatus->buscarPostagemStatus('postagem_status_id', $id);

if ($busca['status'] == 'not_found' || is_integer($id) || $busca['status'] == 'error') {
    header('Location: ?secao=postagens-status');
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
                    <a class="btn btn-success btn-sm custom-nav card-description" href="?secao=postagens-status" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>



            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

                        $dados = [
                            'postagem_status_nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
                            'postagem_status_descricao' => htmlspecialchars($_POST['descricao'], ENT_QUOTES, 'UTF-8')
                        ];

                        $result = $postagensStatus->atualizarPostagemStatus($id, $dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            echo '<script>
                            setTimeout(function(){
                                window.location.href = "?secao=postagem-status&id=' . $id . '";
                            }, 1000);</script>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $postagensStatus->apagarPostagemStatus($id);
                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                                setTimeout(function(){
                                    window.location.href = "?secao=postagens-status";
                                }, 1000);</script>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-12 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome do status" value="<?php echo $busca['dados'][0]['postagem_status_nome'] ?>" required>
                        </div>

                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="descricao" rows="5" placeholder="Descrição do status de postagem"><?php echo $busca['dados'][0]['postagem_status_descricao'] ?></textarea>
                        </div>
                        <div class="col-md-4 col-6">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="fa-regular fa-floppy-disk"></i> Atualizar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>