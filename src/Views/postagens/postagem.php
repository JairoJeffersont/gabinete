<?php


include '../src/Views/includes/verificaLogado.php';

require_once '../autoloader.php';

use GabineteDigital\Controllers\PostagemController;
use GabineteDigital\Controllers\PostagemStatusController;
use GabineteDigital\Middleware\UploadFile;

$postagensStatus = new PostagemStatusController();
$postagens = new PostagemController();
$uploadArquivo = new UploadFile();

$id = $_GET['id'];

$buscaPostagem = $postagens->buscarPostagem('postagem_id', $id);

if ($buscaPostagem['status'] == 'not_found' || is_integer($id) || $buscaPostagem['status'] == 'error') {
    header('Location: ?secao=postagens');
}

$pasta = '../public/arquivos/postagens/' . $buscaPostagem['dados'][0]['postagem_pasta'];

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

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $resultado = $postagens->apagarPostagem($id);

                        if ($resultado['status'] === 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
                            echo '<script>
                                    setTimeout(function(){
                                        window.location.href = "?secao=postagens";
                                    }, 500);
                                </script>';
                        } elseif ($resultado['status'] === 'error' || $resultado['status'] === 'invalid_id' || $resultado['status'] === 'delete_conflict') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $resultado['message'] . '</div>';
                        }
                    }

                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_titulo" placeholder="Título (Post dia das crianças, Aniversário do deputado...)" value="<?php echo $buscaPostagem['dados'][0]['postagem_titulo'] ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_midias" placeholder="Mídias (facebook, instagram, site...)" value="<?php echo $buscaPostagem['dados'][0]['postagem_midias'] ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="date" class="form-control form-control-sm" name="postagem_data" value="<?php echo date('Y-m-d', strtotime($buscaPostagem['dados'][0]['postagem_criada_por'])) ?>" required>
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
                            <textarea class="form-control form-control-sm" name="postagem_informacoes" placeholder="Informações, texto da postagem, legendas...." rows="6" required><?php echo $buscaPostagem['dados'][0]['postagem_informacoes'] ?></textarea>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="fa-solid fa-trash"></i> Apagar</button>

                        </div>
                    </form>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2 ">
                        <div class="card-body p-1">
                            <?php

                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_upload'])) {
                                $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'ai', 'psd', 'mov', 'mp4', 'cdr', 'pdf', 'zip'];

                                $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

                                if (!in_array($extensao, $extensoesPermitidas)) {
                                    echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Tipo de arquivo não permitido.</div>';
                                } else {
                                    $uploadResult = $uploadArquivo->salvarArquivo($pasta, $_FILES['foto']);

                                    if ($uploadResult['status'] == 'upload_ok') {
                                        echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Arquivo salvo com sucesso.</div>';
                                    } else if ($uploadResult['status'] == 'file_not_permitted' || $uploadResult['status'] == 'file_too_large') {
                                        echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Tipo de arquivo não permitido ou muito grande</div>';
                                    } else if ($uploadResult['status'] == 'error' || $uploadResult['status'] == 'forbidden') {
                                        echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Erro interno do servidor</div>';
                                    }
                                }
                            }


                            ?>


                            <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                                <div class="col-md-3 col-12">
                                    <input type="file" class="form-control form-control-sm" name="foto" required>
                                </div>
                                <div class="col-md-3 col-12">
                                    <button type="submit" class="btn btn-primary btn-sm" name="btn_upload"><i class="fa-regular fa-floppy-disk"></i> Salvar Arquivo</button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php

                    if (is_dir($pasta)) {
                        $arquivos = scandir($pasta);
                        $arquivos = array_diff($arquivos, array('.', '..'));

                        if (!empty($arquivos)) {

                            if (isset($_POST['arquivo_para_apagar'])  && isset($_POST['btn_apagar_arquivo'])) {
                                $arquivoParaApagar = $_POST['arquivo_para_apagar'];
                                $caminhoArquivo = $pasta . '/' . $arquivoParaApagar;

                                if (file_exists($caminhoArquivo)) {
                                    unlink($caminhoArquivo);
                                    echo '<script>
                                    setTimeout(function(){
                                        window.location.href = "?secao=postagem&id=' . $id . '";
                                    }, 1);
                                </script>';
                                } else {
                                    echo "<p>O arquivo não existe.</p>";
                                }
                            }

                            echo '<div class="d-flex flex-wrap gap-3">';

                            foreach ($arquivos as $arquivo) {
                                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                                if (in_array($extensao, ['jpg', 'jpeg', 'png'])) {
                                    $arquivo_para_exibir = str_replace('../', '', $pasta) . '/' . $arquivo;
                                    $exibir_midia = '<img class="img-thumbnail" src="' . $arquivo_para_exibir . '" style="width: 100%; height: auto; object-fit: contain;" alt="Imagem" />';
                                } elseif ($extensao === 'zip') {
                                    $arquivo_para_exibir = 'public/img/zip.jpg';
                                    $exibir_midia = '<img class="img-thumbnail" src="' . $arquivo_para_exibir . '" style="width: 100%; height: auto; object-fit: contain;" alt="Imagem" />';
                                } elseif (in_array($extensao, ['mp4', 'mov'])) {
                                    $arquivo_para_exibir = $pasta . '/' . $arquivo;
                                    $exibir_midia = '<video controls style="width: 100%; height: auto;">
                                                        <source src="' . str_replace('../', '', $arquivo_para_exibir) . '" type="video/' . $extensao . '">
                                                        Seu navegador não suporta o elemento de vídeo.
                                                     </video>';
                                } else if ($extensao === 'psd') {
                                    $arquivo_para_exibir = 'public/img/psd.png';
                                    $exibir_midia = '<img class="img-thumbnail" src="' . $arquivo_para_exibir . '" style="width: 100%; height: auto; object-fit: contain;" alt="Imagem" />';
                                } else if ($extensao === 'ai') {
                                    $arquivo_para_exibir = 'public/img/ai.png';
                                    $exibir_midia = '<img class="img-thumbnail" src="' . $arquivo_para_exibir . '" style="width: 100%; height: auto; object-fit: contain;" alt="Imagem" />';
                                }

                                echo '<div style="width: 150px; height: auto;">';
                                echo '<a href="' . str_replace('../', '', $pasta) . '/' . $arquivo . '" target="_blank">';
                                echo $exibir_midia;
                                echo '</a>';
                                echo '<form method="POST">';
                                echo '<input type="hidden" name="arquivo_para_apagar" value="' . $arquivo . '">';
                                echo '<button type="submit" class="btn btn-danger btn-sm mt-2" name="btn_apagar_arquivo">Apagar</button>';
                                echo '</form>';
                                echo '</div>';
                            }


                            echo '</div>';
                        } else {
                            echo '<p class="card-text card-description">Não existem arquivos na pasta</p>';
                        }
                    }

                    ?>
                </div>


            </div>
        </div>
    </div>