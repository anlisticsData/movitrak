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
            position: fixed; /* Alterado para fixed para ficar sempre visível */
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1000; /* Acima do conteúdo, mas abaixo dos toasts */
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 10px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }

        .card-img-top {
            cursor: pointer;
        }
        /* Estilos para o container de toasts */
        .toast-container {
            position: fixed;
            bottom: 80px; /* Ajuste para ficar acima do autoRefreshPanel */
            right: 20px;
            z-index: 1050; /* Garante que o toast fique acima de outros elementos */
            display: flex;
            flex-direction: column-reverse; /* Para que novos toasts apareçam acima dos antigos */
        }
        /* Estilos para a miniatura da imagem na tabela de busca */
        .table-img-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 4px;
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
                <!-- /FIM DO CAMPO DE BUSCA -->

                <!-- MOVIMENTOS RECENTES COM MODAL NAS IMAGENS -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h4>Movimentos Recentes</h4>
                        <div class="card-deck flex-wrap">
                            <?php if (!empty($movimentos_recentes)) : ?>
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
                                                src="../assets/img/no-image.png"
                                                class="card-img-top visualizar-imagem"
                                                data-imagem="../assets/img/no-image.png"
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
                            <?php else : ?>
                                <div class="col-12 text-center py-4">
                                    <p class="lead text-muted">Não há movimentos recentes no momento.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Modal de visualização de imagem (para cards de movimentos recentes e busca) -->
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

                <!-- Novo Modal para Resultados da Busca de Placa -->
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

    <!-- Painel de Auto-Refresh -->
    <div id="autoRefreshPanel" class="d-flex justify-content-center align-items-center">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="autoRefreshToggle">
            <label class="form-check-label" for="autoRefreshToggle">
                Atualização Automática (a cada 1 min)
            </label>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container">
        <!-- Toasts serão adicionados aqui dinamicamente -->
    </div>

    <!-- SCRIPTS (jQuery, Bootstrap, Chart.js) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Variáveis de controle do auto-refresh
        let autoRefreshIntervalId = null;
        let autoRefreshEnabledByUser = false; // Estado do checkbox
        let modalOpenCount = 0; // Contador de modais abertos
        let placaInputFocused = false; // Estado do foco no input de placa

        // Função para exibir um toast
        function showToast(message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                console.error("Toast container not found.");
                return;
            }

            const toastHtml = `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
                    <div class="toast-header">
                        <strong class="mr-auto text-${type}">${type === 'success' ? 'Sucesso' : (type === 'danger' ? 'Erro' : 'Informação')}</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            const newToast = $(toastHtml);
            $(toastContainer).prepend(newToast); // Adiciona o novo toast no topo
            newToast.toast('show');
            newToast.on('hidden.bs.toast', function () {
                $(this).remove(); // Remove o toast do DOM após ser ocultado
            });
        }

        // Função para formatar data e hora
        function formatDateTime(dateTimeString) {
            if (!dateTimeString) return 'N/A';
            const date = new Date(dateTimeString);
            if (isNaN(date.getTime())) return 'N/A'; // Verifica se a data é inválida

            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Meses são 0-indexados
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }

        // --- Funções de Controle do Auto-Refresh ---
        function startAutoRefresh() {
            // Só inicia se o usuário ativou, nenhum modal está aberto e o input não está focado
            if (autoRefreshEnabledByUser && modalOpenCount === 0 && !placaInputFocused) {
                if (autoRefreshIntervalId === null) { // Evita múltiplos intervalos
                    autoRefreshIntervalId = setInterval(function() {
                        location.reload(); // Recarrega a página
                    }, 60000); // 60 segundos = 1 minuto
                    // showToast('Atualização automática ativada.', 'info'); // Opcional: notificar o usuário
                }
            }
        }

        function stopAutoRefresh() {
            if (autoRefreshIntervalId !== null) {
                clearInterval(autoRefreshIntervalId);
                autoRefreshIntervalId = null;
                // showToast('Atualização automática pausada.', 'info'); // Opcional: notificar o usuário
            }
        }

        // --- Event Listeners para Controle do Auto-Refresh ---
        $(document).ready(function() {
            const autoRefreshToggle = $('#autoRefreshToggle');
            const placaBuscaInput = $('#placaBuscaInput');

            // Estado inicial do checkbox (pode ser persistido com localStorage)
            if (localStorage.getItem('autoRefreshEnabled') === 'true') {
                autoRefreshToggle.prop('checked', true);
                autoRefreshEnabledByUser = true;
                startAutoRefresh();
            }

            // Evento de mudança do checkbox
            autoRefreshToggle.on('change', function() {
                autoRefreshEnabledByUser = $(this).is(':checked');
                localStorage.setItem('autoRefreshEnabled', autoRefreshEnabledByUser); // Persistir estado
                if (autoRefreshEnabledByUser) {
                    startAutoRefresh();
                } else {
                    stopAutoRefresh();
                }
            });

            // Eventos de foco/desfoco no input de busca
            placaBuscaInput.on('focus', function() {
                placaInputFocused = true;
                stopAutoRefresh();
            });

            placaBuscaInput.on('blur', function() {
                placaInputFocused = false;
                startAutoRefresh(); // Tenta reiniciar se as condições permitirem
            });

            // Eventos de abertura/fechamento de modais
            $(document).on('show.bs.modal', '.modal', function() {
                modalOpenCount++;
                stopAutoRefresh();
            });

            $(document).on('hidden.bs.modal', '.modal', function() {
                modalOpenCount--;
                if (modalOpenCount === 0) { // Se não há mais modais abertos
                    startAutoRefresh(); // Tenta reiniciar se as condições permitirem
                }
            });
        });


        // Event delegation para abrir o modal de visualização da imagem
        $(document).on('click', '.visualizar-imagem', function() {
            const imagemUrl = $(this).data('imagem');
            $('#imagemModalImg').attr('src', imagemUrl);
            $('#imagemModal').modal('show');
        });

        // Lógica de busca da placa
        const placaBuscaInput = document.getElementById('placaBuscaInput');
        const buscarBtn = document.getElementById('buscarPlacaBtn');

        buscarBtn.addEventListener('click', function() {
            const placa = placaBuscaInput.value.trim().toUpperCase();

            if (!placa) {
                showToast('Digite uma placa para buscar.', 'danger');
                return;
            }

            // Adicionar estado de carregamento no botão
            const originalButtonHtml = buscarBtn.innerHTML;
            buscarBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
            buscarBtn.disabled = true;

            showToast('Buscando placa...', 'info');

            const data = {
                plate: placa,
                user: "<?php echo $userId; ?>"
            };

            const url = "../api/get-plate.php";
            $.post(url, data, function(response) {
                // Restaurar estado do botão
                buscarBtn.innerHTML = originalButtonHtml;
                buscarBtn.disabled = false;

                $('#searchResultsBody').empty(); // Limpa os resultados anteriores

                if (response.success && response.data && response.data.length > 0) {
                    showToast(`Resultados encontrados para a placa: <b>${placa}</b>`, 'success');
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
                        const createdAtFormatted = formatDateTime(movimento.created_at);
                        const statusText = movimento.ocupado ? '<span class="badge badge-danger">OCUPADA</span>' : '<span class="badge badge-success">LIVRE</span>';
                        const rowClass = movimento.ocupado ? 'table-danger' : ''; // Linha vermelha se ocupada

                        tableHtml += `
                            <tr class="${rowClass}">
                                <td>${movimento.fk_vacancie}</td>
                                <td>${placaText}</td>
                                <td>${createdAtFormatted}</td>
                                <td>
                                    <img src="${imageUrl}" class="table-img-thumbnail visualizar-imagem" data-imagem="${imageUrl}" alt="Imagem">
                                </td>
                               
                                <td>
                                    
                                </td>
                            </tr>
                        `;
                    });
                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    $('#searchResultsBody').html(tableHtml);
                    $('#searchResultsModal').modal('show'); // Abre o modal com os resultados
                    placaBuscaInput.value = ''; // Limpa o campo de busca
                } else {
                    showToast(`Placa <b>${placa}</b> não localizada ou sem movimentos recentes.`, 'danger');
                    $('#searchResultsBody').html('<p class="text-center text-muted">Nenhum movimento encontrado para a placa informada.</p>');
                    $('#searchResultsModal').modal('show'); // Abre o modal mesmo sem resultados para mostrar a mensagem
                }
            }).fail(function() {
                // Restaurar estado do botão em caso de falha
                buscarBtn.innerHTML = originalButtonHtml;
                buscarBtn.disabled = false;
                showToast('Erro ao comunicar com o servidor. Tente novamente.', 'danger');
            });
        });
    </script>

</body>

</html>
