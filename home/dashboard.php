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
function placaValida($placa)
{
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
$placasPorDia = [];
$contagemPorDia = [];

foreach ($movimentos_ultimos_dias as $movimento) {
    $placa = strtoupper($movimento['placa'] ?? '');
    $createdAt = strtotime($movimento['created_at']);

    $dia = date('D', $createdAt);

    if (!isset($placasPorDia[$dia])) {
        $placasPorDia[$dia] = [];
    }

    // Se essa placa já foi contada nesse dia, ignore
    if (in_array($placa, $placasPorDia[$dia])) continue;

    if ($placa != "OCR_FAILED") {
        $placasPorDia[$dia][] = $placa;

        if (!isset($contagemPorDia[$dia])) {
            $contagemPorDia[$dia] = 1;
        } else {
            $contagemPorDia[$dia]++;
        }
    }
}

// Organizar os dados para o gráfico
$movimentos_dias = array_keys($contagemPorDia);
$movimentos_contagem = array_values($contagemPorDia);

// Traduzindo os dias da semana para português
$dias_da_semana = ['Sun' => 'Domingo', 'Mon' => 'Segunda', 'Tue' => 'Terça', 'Wed' => 'Quarta', 'Thu' => 'Quinta', 'Fri' => 'Sexta', 'Sat' => 'Sábado'];
$dias_em_portugues = array_map(function ($dia) use ($dias_da_semana) {
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

        .toast-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 1050;
            display: flex;
            flex-direction: column-reverse;
        }

        .custom-switch {
            padding-left: 2.25rem;
        }
        .custom-switch .custom-control-label::before {
            left: -2.25rem;
            width: 1.75rem;
            height: 1rem;
            background-color: #d1d4d7;
        }
        .custom-switch .custom-control-label::after {
            top: calc(0.25rem + 2px);
            left: calc(-2.25rem + 4px);
            width: calc(1rem - 4px);
            height: calc(1rem - 4px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <?php include('../includes/components/header.php'); ?>
        <div class="row">
            <?php include('../includes/components/sidebar.php'); ?>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Dashboard</h2>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="autoRefreshToggle">
                        <label class="custom-control-label" for="autoRefreshToggle">Auto Refresh</label>
                    </div>
                </div>

                <!-- Resto do código HTML anterior permanece o mesmo -->
                <!-- [Todo o código HTML anterior do dashboard] -->
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
                    label: 'Placas únicas/dia',
                    data: <?php echo json_encode($movimentos_contagem); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Variáveis para controle de auto-refresh
        let autoRefreshInterval = null;
        let isAutoRefreshEnabled = false;

        // Função para iniciar o auto-refresh
        function startAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }

            autoRefreshInterval = setInterval(function() {
                if (!isModalOpen()) {
                    location.reload();
                }
            }, 5 * 60 * 1000); // 5 minutos
        }

        // Função para parar o auto-refresh
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }

        // Verificar se algum modal está aberto
        function isModalOpen() {
            return $('.modal:visible').length > 0;
        }

        // Evento de toggle do auto-refresh
        $('#autoRefreshToggle').on('change', function() {
            isAutoRefreshEnabled = $(this).prop('checked');

            if (isAutoRefreshEnabled) {
                startAutoRefresh();
                showToast('Auto Refresh ativado. A página será atualizada a cada 5 minutos.', 'info');
            } else {
                stopAutoRefresh();
                showToast('Auto Refresh desativado.', 'info');
            }
        });

        // Adicionar eventos para pausar/retomar refresh com modais
        $(document).on('show.bs.modal', function() {
            if (isAutoRefreshEnabled) {
                stopAutoRefresh();
            }
        });

        $(document).on('hidden.bs.modal', function() {
            if (isAutoRefreshEnabled) {
                startAutoRefresh();
            }
        });

        // Função para adicionar animação de refresh
        function addRefreshAnimation() {
            if (isAutoRefreshEnabled) {
                $('body').append(`
                    <div id="refresh-loader" style="
                        position: fixed; 
                        bottom: 20px; 
                        right: 20px; 
                        background: rgba(0,0,0,0.7); 
                        color: white; 
                        padding: 10px; 
                        border-radius: 5px; 
                        z-index: 9999;
                    ">
                        <div class="spinner-border text-light" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                        Atualizando em breve...
                    </div>
                `);

                setTimeout(() => {
                    $('#refresh-loader').fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        }

        // Resto do seu código JavaScript anterior
        // [Manter as funções de busca de placa, visualização de imagem, etc.]
    </script>
</body>
</html>
