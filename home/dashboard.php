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

// Função para validar placa brasileira (modelo antigo ou Mercosul)
function placaValida($placa) {
    $placa = strtoupper(preg_replace('/[^A-Z0-9]/', '', $placa)); // remove caracteres inválidos

    // Padrão antigo: ABC1234
    if (preg_match('/^[A-Z]{3}[0-9]{4}$/', $placa)) {
        return true;
    }

    // Padrão Mercosul: ABC1D23
    if (preg_match('/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', $placa)) {
        return true;
    }

    // Placas especiais (7 ou 8 caracteres alfanuméricos)
    if (preg_match('/^[A-Z0-9]{7,8}$/', $placa)) {
        return true;
    }

    return false;
}


// Buscando dados
$movimentos_ultimos_dias = $movimentVacanciesDAO->getMovimentosUltimosDiasPorUsuario($userId);
$movimentos_recentes = $movimentVacanciesDAO->getMovimentosRecentesPorUsuario($userId);

// Processando dados para o gráfico com filtro de 1h e placas válidas
$placasPorDia = []; // Exemplo: [ 'Mon' => ['ABC1234', 'DEF5678'] ]
$contagemPorDia = [];

foreach ($movimentos_ultimos_dias as $movimento) {
    $placa = strtoupper($movimento['placa'] ?? '');
    $createdAt = strtotime($movimento['created_at']);

    if (!placaValida($placa)) continue;

    $dia = date('D', $createdAt);

    if (!isset($placasPorDia[$dia])) {
        $placasPorDia[$dia] = [];
    }

    // Se essa placa já foi contada nesse dia, ignore
    if (in_array($placa, $placasPorDia[$dia])) continue;

    $placasPorDia[$dia][] = $placa;

    if (!isset($contagemPorDia[$dia])) {
        $contagemPorDia[$dia] = 1;
    } else {
        $contagemPorDia[$dia]++;
    }
}

// Organizar os dados para o gráfico
$movimentos_dias = array_keys($contagemPorDia);
$movimentos_contagem = array_values($contagemPorDia);

// Traduzindo os dias da semana para português
$dias_da_semana = ['Sun' => 'Domingo', 'Mon' => 'Segunda', 'Tue' => 'Terça', 'Wed' => 'Quarta', 'Thu' => 'Quinta', 'Fri' => 'Sexta', 'Sat' => 'Sábado'];
$dias_em_portugues = array_map(function($dia) use ($dias_da_semana) {
    return $dias_da_semana[$dia];
}, $movimentos_dias);
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
        /* Estilos personalizados */
        main {
            padding-bottom: 60px;
        }
        /* Container de toasts */
        .toast-container {
            position: fixed;
            bottom: 80px; 
            right: 20px;
            z-index: 1050; 
            display: flex;
            flex-direction: column-reverse; 
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
                    </div>
                </div>
                <!-- Gráfico de Movimentos Diários -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Movimentos por Dia</h4>
                        <canvas id="movimentosChart"></canvas>
                    </div>
                </div>

                <!-- MOVIMENTOS RECENTES COM MODAL NAS IMAGENS -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Movimentos Recentes</h4>
                        <div class="card-deck flex-wrap">
                            <?php if (!empty($movimentos_recentes)) : ?>
                                <?php foreach ($movimentos_recentes as $movimento) : ?>
                                    <div class="card mb-3" style="max-width: 300px;">
                                        <?php if (!empty($movimento['file_path'])) : ?>
                                            <img src="../<?php echo htmlspecialchars($movimento['file_path']); ?>" class="card-img-top visualizar-imagem" data-imagem="../<?php echo htmlspecialchars($movimento['file_path']); ?>" alt="Imagem do movimento" style="height: 180px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="../assets/img/no-image.png" class="card-img-top visualizar-imagem" data-imagem="../assets/img/no-image.png" alt="Sem imagem" style="height: 180px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title">ID da Vaga: <?php echo $movimento['fk_vacancie']; ?></h5>
                                            <p class="card-text">Placa: <?php echo $movimento['placa'] ?? 'N/A'; ?></p>
                                            <a href="../cameras/historical?id=<?php echo $movimento['fk_vacancie']; ?>" class="btn btn-primary mt-2">Ver Histórico</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="col-12 text-center py-4">
                                    <p class="lead text-muted">Não há movimentos recentes no momento.</p>
                                </div>
                            <?php endif; ?>
                        </div>
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

                <!-- Modal para Resultados da Busca por Placa -->
                <div class="modal fade" id="searchResultsModal" tabindex="-1" aria-labelledby="searchResultsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="searchResultsModalLabel">Resultados da Busca por Placa</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="searchResultsBody">
                                <!-- A tabela de resultados da busca será injetada aqui -->
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- SCRIPTS (jQuery, Bootstrap, Chart.js) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Configuração do gráfico de movimentos diários
        const ctx = document.getElementById('movimentosChart').getContext('2d');
        const movimentosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dias_em_portugues); ?>,
                datasets: [{
                    label: 'Movimentos Diários',
                    data: <?php echo json_encode($movimentos_contagem); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        // Lógica de busca de placa
        const placaBuscaInput = document.getElementById('placaBuscaInput');
        const buscarBtn = document.getElementById('buscarPlacaBtn');

        buscarBtn.addEventListener('click', function() {
            const placa = placaBuscaInput.value.trim().toUpperCase();

            if (!placa) {
                showToast('Digite uma placa para buscar.', 'danger');
                return;
            }

            const originalButtonHtml = buscarBtn.innerHTML;
            buscarBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
            buscarBtn.disabled = true;

            const data = {
                plate: placa,
                user: "<?php echo $userId; ?>"
            };

            const url = "../api/get-plate.php";
            $.post(url, data, function(response) {
                buscarBtn.innerHTML = originalButtonHtml;
                buscarBtn.disabled = false;

                $('#searchResultsBody').empty();
                if (response.success && response.data && response.data.length > 0) {
                    let tableHtml = `
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID Vaga</th>
                                        <th>Placa</th>
                                        <th>Registrado em</th>
                                        <th>Imagem</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    response.data.forEach(function(movimento) {
                        const imageUrl = movimento.file_path ? `../${movimento.file_path}` : '../assets/img/no-image.png';
                        const placaText = movimento.placa ? movimento.placa : 'N/A';
                        const createdAtFormatted = new Date(movimento.created_at).toLocaleString('pt-BR');
                        tableHtml += `
                            <tr>
                                <td>${movimento.fk_vacancie}</td>
                                <td>${placaText}</td>
                                <td>${createdAtFormatted}</td>
                                <td>
                                    <img src="${imageUrl}"   class="table-img-thumbnail visualizar-imagem" data-imagem="${imageUrl}" alt="Imagem" style="max-width: 80px; max-height: 80px;">
                                </td>
                                <td></td>
                            </tr>
                        `;
                    });
                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    $('#searchResultsBody').html(tableHtml);
                    $('#searchResultsModal').modal('show');
                } else {
                    showToast('Placa não localizada ou sem movimentos recentes.', 'danger');
                    $('#searchResultsBody').html('<p class="text-center text-muted">Nenhum movimento encontrado para a placa informada.</p>');
                    $('#searchResultsModal').modal('show');
                }
            }).fail(function() {
                buscarBtn.innerHTML = originalButtonHtml;
                buscarBtn.disabled = false;
                showToast('Erro ao comunicar com o servidor. Tente novamente.', 'danger');
            });
        });

        // Event delegation para abrir o modal de visualização da imagem
        $(document).on('click', '.visualizar-imagem', function() {
            const imagemUrl = $(this).data('imagem');
            $('#imagemModalImg').attr('src', imagemUrl);
            $('#imagemModal').modal('show');
        });

        // Função para exibir um toast
        function showToast(message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');
            const toastHtml = `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
                    <div class="toast-header">
                        <strong class="mr-auto text-${type}">${type === 'success' ? 'Sucesso' : (type === 'danger' ? 'Erro' : 'Informação')}</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `;
            const newToast = $(toastHtml);
            $(toastContainer).prepend(newToast);
            newToast.toast('show');
            newToast.on('hidden.bs.toast', function () {
                $(this).remove();
            });
        }
    </script>
</body>
</html>
