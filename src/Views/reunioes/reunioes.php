<?php

use GabineteDigital\Controllers\ReunioesController;

include '../src/Views/includes/verificaLogado.php';
require_once '../autoloader.php';

$reunioesController = new ReunioesController();

$data = $_GET['data'] ?? date('Y-m-d');
$tipo = $_GET['tipo'] ?? 'Reunião Deliberativa';

$buscaReunioes = $reunioesController->buscarReunioes($data);

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
                <div class="card-header bg-primary text-white px-2 py-1 card-background"><i class="bi bi-calendar3"></i> Reuniões e sessões do dia</div>
                <div class="card-body p-2">
                    <p class="card-text mb-1">Consulte todas as reuniões sessões da Câmara </p>
                    <p class="card-text mb-0"><i class="bi bi-info-circle-fill"></i> Os dados são atualizados em tempo real. Se nenhuma resposta for exibida, tente clicar em <b>"Buscar"</b> novamente.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="secao" value="reunioes" />
                        <div class="col-md-1 col-4">
                            <input type="date" class="form-control form-control-sm" name="data" value="<?php echo $data ?>">
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="tipo" required>
                                <?php

                                if ($buscaReunioes['status'] == 'success') {
                                    $tiposReunioes = [];

                                    foreach ($buscaReunioes['dados'] as $reuniao) {
                                        $descricaoTipo = $reuniao['descricaoTipo'];
                                        $tiposReunioes[$descricaoTipo] = true;
                                    }

                                    ksort($tiposReunioes);

                                    foreach ($tiposReunioes as $descricaoTipo => $_) {
                                        $selected = ($descricaoTipo == $tipo) ? 'selected' : '';
                                        echo '<option value="' . $descricaoTipo . '" ' . $selected . '>' . $descricaoTipo . '</option>';
                                    }
                                } else {
                                    echo '<option value="Reunião Deliberativa">Não disponível</option>';
                                }
                                ?>

                            </select>
                        </div>

                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <?php

            if ($buscaReunioes['status'] == 'success') {

                usort($buscaReunioes['dados'], function ($a, $b) {
                    return strtotime($a['dataHoraInicio']) - strtotime($b['dataHoraInicio']);
                });

                $horarios = [];
                $reunioesFiltradas = [];

                foreach ($buscaReunioes['dados'] as $hora) {
                    if ($hora['descricaoTipo'] == $tipo) {
                        $dataHoraInicio = $hora['dataHoraInicio'];
                        if (!in_array($dataHoraInicio, $horarios)) {
                            $horarios[] = $dataHoraInicio;
                            $reunioesFiltradas[] = $hora;
                        }
                    }
                }

                foreach ($horarios as $horario) {
                    echo '<div class="card mb-2">
                            <div class="card-header bg-secondary text-white px-2 py-1" style="font-size:12px"><i class="bi bi-alarm-fill"></i> <b>' . date('H:i', strtotime($horario)) . '</b></div>
                            <div class="card-body p-2">
                            <div class="accordion" id="accordionPanelsStayOpenExample">';

                    foreach ($buscaReunioes['dados'] as $comissao) {
                        if ($comissao['dataHoraInicio'] == $horario && $comissao['descricaoTipo'] == $tipo) {
                            $sigla = $comissao['orgaos'][0]['sigla'];
                            $nome = $comissao['orgaos'][0]['nomePublicacao'];
                            $local = $comissao['localCamara']['nome'];
                            $situacao = $comissao['situacao'];
                            $id = $comissao['id'];

                            echo '<div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" style="font-size:12px" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $id . '" aria-expanded="false" aria-controls="panelsStayOpen-collapse' . $id . '">
                                    ' . ($situacao == 'Em Andamento' ? '<span style="color:green; font-weight:500">' . $sigla . ' ' . $nome . '</span>' : ($situacao == 'Encerrada' || $situacao == 'Cancelada' || $situacao == 'Encerrada (Termo)' || $situacao == 'Encerrada (Final)'  || $situacao == 'Suspensa' ? '<span style="color:red; font-weight:500"">' . $sigla . ' | ' . $nome . '</span>' : ($situacao == 'Convocada' ? '<span style="color:blue">' . $sigla . ' | ' . $nome . '</span>' :
                                $sigla . ' ' . $nome))) . '
                                </button>
                            </h2>
    
                                <div id="panelsStayOpen-collapse' . $id . '" class="accordion-collapse collapse">
                                    <div class="accordion-body" style="font-size:12px">
                                        <p class="mb-1"><i class="bi bi-house-door"></i> ' . $local . '</p>
                                        <p class="mb-3"><i class="bi bi-info-circle"></i> ' . $situacao . '</p>
                                        <p class="mb-3">' . mb_strimwidth($comissao['descricao'], 0, 800, '...') . '</p>';

                            if ($tipo == 'Reunião Deliberativa' || $tipo == 'Sessão Deliberativa') {

                                echo '<a href="?secao=pauta&reuniao=' . $comissao['id'] . '" onclick="return confirmarRedirecionamento();" type="button" class="btn btn-primary btn-sm" style="font-size:0.8em"><i class="bi bi-file-earmark-text-fill"></i> Ver pauta</a>';
                              
                            } else {
                                echo '<button type="button" class="btn disabled btn-primary btn-sm" style="font-size:0.8em"><i class="bi bi-file-earmark-text-fill"></i> Ver pauta</button>';
                            }

                            echo '&nbsp;<a href="https://www.camara.leg.br/evento-legislativo/' . $comissao['id'] . '" target="_blank" type="button" class="btn btn-secondary btn-sm" style="font-size:0.8em"><i class="bi bi-file-earmark-text-fill"></i> Página da Câmara</a>';

                            echo '&nbsp;<a href="' . (!empty($comissao['urlRegistro']) ? $comissao['urlRegistro'] : '#') . '" target="_blank" type="button" class="btn ' . (empty($comissao['urlRegistro']) ? 'disabled' : '') . ' btn-danger btn-sm" style="font-size:0.8em"><i class="bi bi-youtube"></i> Youtube</a>';

                            echo '</div>
                                </div>
                            </div>';
                        }
                    }
                    echo '</div></div></div>';
                }
            } else if ($buscaReunioes['status'] == 'empty' || $buscaReunioes['status'] == 'error') {
                echo '<div class="card shadow-sm mb-2"><div class="card-body p-2">' . $buscaReunioes['message'] . '</div></div>';
            }


            ?>
        </div>
    </div>
</div>
<script>
    function confirmarRedirecionamento() {
        var resposta = confirm("A pauta pode ser extensa e levar alguns minutos para carregar. Deseja continuar aguardando?");
        if (resposta) {
            return true; // O link será acessado
        } else {
            return false; // O redirecionamento é cancelado
        }
    }
</script>