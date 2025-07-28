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

// Dados para o gráfico
$movimentos_ultimos_dias = $movimentVacanciesDAO->getMovimentosUltimosDiasPorUsuario($userId);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        h2 {
            color: #007bff;
        }

        input {
            border-radius: 10px;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .toast-container {
            bottom: 90px;
        }

        #movimentosSemanaChart {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <?php include('../includes/components/header.php'); ?>
        <div class="row">
            <?php include('../includes/components/sidebar.php'); ?>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h2>Dashboard de Movimentos de Vagas</h2>

                <!-- CAMPO DE BUSCA DE PLACA DESTACADO -->
                <div class="row mt-4">
                    <div class="col-lg-7 col-md-9 mx-auto">
                        <div class="input-group input-group-lg mb-3 shadow">
                            <input type="text" id="placaBuscaInput" class="form-control" placeholder="Digite a placa..." aria-label="Buscar placa" autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="buscarPlacaBtn">
                                    <i class="fas fa-search"></i> Buscar Placa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /FIM DO CAMPO DE BUSCA -->

                <!-- Canvas para o gráfico de movimentos da semana -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Movimentos da Semana</h4>
                        <canvas id="movimentosSemanaChart"></canvas>
                    </div>
                </div>

                <!-- MOVIMENTOS RECENTES -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Movimentos Recentes</h4>
                        <div class="card-deck flex-wrap">
                            <?php $movimentos_recentes = $movimentVacanciesDAO->getMovimentosRecentesPorUsuario($userId); ?>
                            <?php if (!empty($movimentos_recentes)): ?>
                                <?php foreach ($movimentos_recentes as $movimento): ?>
                                    <div class="card mb-3">
                                        <?php if (!empty($movimento['file_path'])): ?>
                                            <img src="../<?php echo htmlspecialchars($movimento['file_path']); ?>" class="card-img-top visualizar-imagem" data-imagem="../<?php echo htmlspecialchars($movimento['file_path']); ?>" alt="Imagem do movimento" style="height: 180px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="../assets/img/no-image.png" class="card-img-top visualizar-imagem" data-imagem="../assets/img/no-image.png" alt="Sem imagem" style="height: 180px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title">ID da Vaga: <?php echo $movimento['fk_vacancie']; ?></h5>
                                            <p class="card-text">Placa: <?php echo $movimento['placa'] ?? 'N/A'; ?></p>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Registrado em: <?php echo converterParaSaoPaulo(date('d/m/Y H:i', strtotime($movimento['created_at']))); ?>
                                                </small>
                                            </p>
                                            <a href="../cameras/historical?id=<?php echo $movimento['fk_vacancie']; ?>" class="btn btn-primary mt-2">Ver Histórico</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-4">
                                    <p class="lead text-muted">Não há movimentos recentes no momento.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container">
        <!-- Toasts serão adicionados aqui dinamicamente -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dados para o gráfico
        const movimentCounts = <?php echo json_encode($movimentos_contagem); ?>;
        const movimentDays = <?php echo json_encode($movimentos_dias); ?>;

        $(document).ready(function() {
            // Configuração do gráfico
            const ctx = document.getElementById('movimentosSemanaChart').getContext('2d');
            const movimentosSemanaChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: movimentDays, // Dias da semana
                    datasets: [{
                        label: 'Número de Movimentos',
                        data: movimentCounts, // Contagem de movimentos
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantidade'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Dias da Semana'
                            }
                        }
                    }
                }
            });

            // Aqui você pode adicionar mais funcionalidades e interações
        });
    </script>

</body>

</html>
