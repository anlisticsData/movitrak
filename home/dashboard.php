<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/dao/MovimentVacanciesDAO.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$userId = $_SESSION['user_id'];



$movimentVacanciesDAO = new MovimentVacanciesDAO($pdo);

$movimentos_ultimos_dias = $movimentVacanciesDAO->getMovimentosUltimosDiasPorUsuario($userId);
$movimentos_recentes = $movimentVacanciesDAO->getMovimentosRecentesPorUsuario($userId);
$movimentos_do_dia = $movimentVacanciesDAO->getResumoMovimentosDoDiaPorUsuario($userId);
$vagas_status = $movimentVacanciesDAO->getVagasStatusPorUsuario($userId);
$vaga_maior_tempo = $movimentVacanciesDAO->getVagaMaiorTempoPorUsuario($userId);
$vaga_maior_frequencia = $movimentVacanciesDAO->getVagaMaiorFrequenciaPorUsuario($userId);

$movimentos_dias = [];
$movimentos_contagem = [];
foreach ($movimentos_ultimos_dias as $movimento) {
    $dia = date('D', strtotime($movimento['created_at']));
    if (!in_array($dia, $movimentos_dias)) {
        $movimentos_dias[] = $dia;
        $movimentos_contagem[] = 1;
    } else {
        $index = array_search($dia, $movimentos_dias);
        $movimentos_contagem[$index]++;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        main {
            padding-bottom: 60px;
        }

        #autoRefreshPanel {
            position: sticky;
            bottom: 70px;
            z-index: 999;
            background-color: #f8f9fa;
        }

        .card-img-top {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <?php include('../includes/components/header.php'); ?>
        <div class="row">
            <?php include('../includes/components/sidebar.php'); ?>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h2>Dashboard</h2>

                <!-- CAMPO DE BUSCA DE PLACA DESTACADO -->
                <div class="row mt-4">
                    <div class="col-lg-7 col-md-9 mx-auto">
                        <div class="input-group input-group-lg mb-3 shadow" style="background:#f8f9fa;border-radius:10px;border:2px solid #007bff;">
                            <input
                                type="text"
                                id="placaBuscaInput"
                                class="form-control"
                                placeholder="Digite a placa ..."
                                aria-label="Buscar placa"
                                autocomplete="off">
                            <div class="input-group-append">
                                <button
                                    class="btn btn-primary"
                                    type="button"
                                    id="buscarPlacaBtn"><i class="fas fa-search"></i> Buscar Placa</button>
                            </div>
                        </div>
                        <div id="placaBuscaFeedback" style="min-height:24px;" class="text-center"></div>
                    </div>
                </div>
                <!-- /FIM DO CAMPO DE BUSCA -->

                <div id="autoRefreshPanel" class="d-flex justify-content-between align-items-center bg-light p-3 border shadow-sm">
                    <div>
                        <strong>Auto-Refresh:</strong>
                        <span id="refreshTimer">Atualizando em 60s</span>
                    </div>
                    <div>
                        <button id="manualRefreshBtn" class="btn btn-primary btn-sm">Atualizar Agora</button>
                        <button id="toggleAutoRefreshBtn" class="btn btn-secondary btn-sm ml-2">Desabilitar Auto-Refresh</button>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h4>Indicadores de Frequência</h4>
                        <canvas id="frequencyChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h4>Status das Vagas</h4>
                        <div class="alert alert-info">
                            <strong>Vagas Ocupadas:</strong> <?php echo ($vagas_status['ocupadas'] - $vagas_status['livres']); ?>
                        </div>
                        <div class="alert alert-success">
                            <strong>Vagas Livres:</strong> <?php echo $vagas_status['livres']; ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h4>Vaga com Maior Frequência</h4>
                        <?php
                        if ($vaga_maior_frequencia !== null && is_array($vaga_maior_frequencia)) {
                            echo "<div class='alert alert-warning'>";
                            echo "<strong>ID da Vaga:</strong> " . $vaga_maior_frequencia['fk_vacancie'] . "<br>";
                            echo "<strong>Movimentos Registrados:</strong> " . $vaga_maior_frequencia['frequencia'] . " movimentos";
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Não foi possível identificar a vaga com maior frequência.</div>";
                        }
                        ?>
                    </div>
                    <div class="col-md-6">
                        <h4>Vaga com Maior Tempo de Permanência</h4>
                        <?php
                        if ($vaga_maior_tempo !== null && is_array($vaga_maior_tempo)) {
                            echo "<div class='alert alert-warning'>";
                            echo "<strong>ID da Vaga:</strong> " . $vaga_maior_tempo['fk_vacancie'] . "<br>";
                            echo "<strong>Tempo de Permanência:</strong> " . $vaga_maior_tempo['tempo_permanencia'] . " horas";
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Não há vagas ocupadas no momento ou a consulta não retornou dados válidos.</div>";
                        }
                        ?>
                    </div>
                </div>

                <!-- MOVIMENTOS RECENTES COM MODAL NAS IMAGENS -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Movimentos Recentes</h4>
                        <div class="card-deck flex-wrap">
                            <?php foreach ($movimentos_recentes as $movimento) : ?>
                                <?php
                                $isOcupado = isset($movimento['ocupado']) && $movimento['ocupado'];
                                $cardClass = $isOcupado ? 'border-danger bg-light' : '';
                                ?>
                                <div class="card mb-3 <?php echo $cardClass; ?>" style="max-width: 300px;">
                                    <?php if (!empty($movimento['file_path'])) : ?>
                                        <img
                                            src="../<?php echo htmlspecialchars($movimento['file_path']); ?>"
                                            class="card-img-top visualizar-imagem"
                                            data-imagem="../<?php echo htmlspecialchars($movimento['file_path']); ?>"
                                            alt="Imagem do movimento"
                                            style="height: 180px; object-fit: cover;">
                                    <?php else: ?>
                                        <img
                                            src="<?php echo $movimento['fk_vacancie']; ?>"
                                            class="card-img-top visualizar-imagem"
                                            data-imagem="<?php echo $movimento['fk_vacancie']; ?>"
                                            alt="Sem imagem"
                                            style="height: 180px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title">ID da Vaga: <?php echo $movimento['fk_vacancie']; ?></h5>
                                        <p class="card-text">Placa: <?php echo $movimento['placa'] ?? 'N/A'; ?></p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Registrado em: <?php echo converterParaSaoPaulo(date('d/m/Y H:i', strtotime($movimento['created_at']))); ?>
                                            </small>
                                        </p>
                                        <a href="../cameras/historical?id=<?php echo $movimento['fk_vacancie']; ?>" class="btn btn-primary mt-2">
                                            Ver Histórico
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Movimentos do Dia (Resumo por Vaga)</h4>
                        <ul class="list-group">
                            <?php foreach ($movimentos_do_dia as $movimento): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($movimento['vaga_name']); ?></strong>
                                        <small class="text-muted">(<?php echo htmlspecialchars($movimento['camera_name']); ?>)</small><br>
                                        <span>Última placa:
                                            <strong>
                                                <?php echo !empty($movimento['ultima_placa']) ? htmlspecialchars($movimento['ultima_placa']) : 'N/A'; ?>
                                            </strong>
                                        </span><br>
                                        <?php if (!empty($movimento['ultima_data']) && strtotime($movimento['ultima_data']) !== false): ?>
                                            <small class="text-muted">
                                                Última atualização:
                                                <?php echo date('d/m/Y H:i', strtotime($movimento['ultima_data'])); ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted text-danger">
                                                Sem movimentação registrada hoje
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="badge badge-primary badge-pill">
                                            <?php echo $movimento['total_movimentos']; ?> movimentos
                                        </span>
                                        <a href="../cameras/historical?id=<?php echo $movimento['vaga_id']; ?>"
                                            class="btn btn-sm btn-outline-secondary ml-2">Ver Histórico</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (empty($movimentos_do_dia)): ?>
                            <div class="alert alert-info mt-2">
                                Nenhuma movimentação registrada hoje.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="height: 80px;"></div>
            </main>
        </div>
    </div>

    <!-- Modal de visualização de imagem -->
    <div class="modal fade" id="imagemModal" tabindex="-1" aria-labelledby="imagemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagemModalLabel">Visualizar Imagem</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="imagemModalImg" class="img-fluid" alt="Visualização da imagem">
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS (jQuery, Bootstrap, Chart.js) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Gráfico de Frequência
        var ctx = document.getElementById('frequencyChart').getContext('2d');
        var frequencyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($movimentos_dias); ?>,
                datasets: [{
                    label: 'Movimentos por Dia',
                    data: <?php echo json_encode($movimentos_contagem); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script>
        let refreshInterval = 10;
        let remainingTime = refreshInterval;
        let autoRefreshEnabled = true;
        let intervalId;
        const timerElement = document.getElementById('refreshTimer');
        const toggleButton = document.getElementById('toggleAutoRefreshBtn');
        const manualButton = document.getElementById('manualRefreshBtn');

        function updateTimerDisplay() {
            timerElement.textContent = `Atualizando em ${remainingTime}s`;
        }

        function startAutoRefresh() {   
            intervalId = setInterval(() => {
                remainingTime--;
                updateTimerDisplay();
                if (remainingTime <= 0) {
                    location.reload();
                }
            }, 60*1000);
        }

        function stopAutoRefresh() {
            clearInterval(intervalId);
        }
        toggleButton.addEventListener('click', () => {
            autoRefreshEnabled = !autoRefreshEnabled;
            if (autoRefreshEnabled) {
                remainingTime = refreshInterval;
                updateTimerDisplay();
                startAutoRefresh();
                toggleButton.textContent = "Desabilitar Auto-Refresh";
                toggleButton.classList.remove('btn-success');
                toggleButton.classList.add('btn-secondary');
            } else {
                stopAutoRefresh();
                timerElement.textContent = "Auto-Refresh desabilitado";
                toggleButton.textContent = "Habilitar Auto-Refresh";
                toggleButton.classList.remove('btn-secondary');
                toggleButton.classList.add('btn-success');
            }
        });
        manualButton.addEventListener('click', () => {
            location.reload();
        });
        if (autoRefreshEnabled) {
            updateTimerDisplay();
            startAutoRefresh();
        }
    </script>

    <script>
        const placaBuscaInput = document.getElementById('placaBuscaInput');
        const buscarBtn = document.getElementById('buscarPlacaBtn');
        const feedback = document.getElementById('placaBuscaFeedback');

        placaBuscaInput.addEventListener('focus', function() {
            if (typeof stopAutoRefresh === "function" && autoRefreshEnabled) stopAutoRefresh();
        });
        placaBuscaInput.addEventListener('blur', function() {
            if (typeof startAutoRefresh === "function" && autoRefreshEnabled) {
                remainingTime = refreshInterval;
                updateTimerDisplay();
                startAutoRefresh();
            }
        });

        buscarBtn.addEventListener('click', function() {
            const placa = placaBuscaInput.value.trim().toUpperCase();
            if (!placa) {
                feedback.innerHTML = '<span class="text-danger">Digite uma placa para buscar.</span>';
                return;
            }

            const data = {
                plate: placa, // placa
                user: "<?php echo $userId;  ?>" // usuário
            };

            const url = "../api/get-plate.php";
            $.post(url, data, function(response) {
                console.log("✅ Resposta:", response);
                alert("Resposta: " + response);
            }).fail(function(xhr, status, error) {
                console.error("❌ Erro:", error);
                alert("Erro ao enviar dados!");
            });





            feedback.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Buscando placa...</span>';
            setTimeout(() => {
                feedback.innerHTML = `<span class="text-success">Resultado para: <b>${placa}</b> (aqui você exibe os dados reais ou filtra cards)</span>`;
            }, 700);
        });

        placaBuscaInput.addEventListener('keyup', function(e) {
            if (e.key === "Enter" || e.keyCode === 13) buscarBtn.click();
        });
    </script>

    <!-- Controle do auto-refresh ao abrir/fechar o modal de imagem -->
    <script>
        $(document).on('click', '.visualizar-imagem', function() {
            var src = $(this).attr('data-imagem');
            $('#imagemModalImg').attr('src', src);
            $('#imagemModal').modal('show');
        });

        $('#imagemModal').on('show.bs.modal', function() {
            if (typeof stopAutoRefresh === "function" && autoRefreshEnabled) {
                stopAutoRefresh();
            }
        });

        $('#imagemModal').on('hidden.bs.modal', function() {
            if (autoRefreshEnabled && typeof startAutoRefresh === "function") {
                remainingTime = refreshInterval;
                updateTimerDisplay();
                startAutoRefresh();
            }
        });
    </script>
</body>

</html>