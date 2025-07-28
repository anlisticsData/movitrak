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
        /* Estilos para o container de toasts */
        .toast-container {
            position: fixed;
            bottom: 80px; /* Ajuste para ficar acima do autoRefreshPanel, se houver */
            right: 20px;
            z-index: 1050; /* Garante que o toast fique acima de outros elementos */
            display: flex;
            flex-direction: column-reverse; /* Para que novos toasts apareçam acima dos antigos */
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
                                        <!-- Fallback para quando não há imagem. Use uma imagem padrão ou um placeholder. -->
                                        <img
                                            src="../assets/img/no-image.png" <!-- Caminho para uma imagem de placeholder -->
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
                    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Alterado para modal-lg para um único card -->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="searchResultsModalLabel">Último Movimento da Placa</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body d-flex justify-content-center" id="searchResultsBody">
                                <!-- O card do último movimento da placa será injetado aqui -->
                            </div>
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


    <!-- SCRIPTS (jQuery, Bootstrap, Chart.js) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
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

        // Event delegation para abrir o modal de visualização da imagem
        // Isso permite que imagens adicionadas dinamicamente também abram o modal
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
                showToast('Por favor, digite uma placa para buscar.', 'danger');
                return;
            }

            // Exibe um toast de "Buscando..."
            showToast('Buscando placa...', 'info');

            const data = {
                plate: placa,
                user: "<?php echo $userId; ?>"
            };

            const url = "../api/get-plate.php";
            $.post(url, data, function(response) {
                // Limpa os resultados anteriores no corpo do modal de busca
                $('#searchResultsBody').empty();

                if (response.success && response.data && response.data.length > 0) {
                    // Pega apenas o primeiro (mais recente) movimento
                    const latestMovimento = response.data[0];

                    showToast(`Último movimento para a placa <b>${placa}</b> encontrado.`, 'success');

                    let cardHtml = '';
                    const imageUrl = latestMovimento.file_path ? `../${latestMovimento.file_path}` : '../assets/img/no-image.png'; // Fallback image
                    const placaText = latestMovimento.placa ? latestMovimento.placa : 'N/A';
                    const createdAtFormatted = formatDateTime(latestMovimento.created_at);
                    const isOcupado = latestMovimento.ocupado; // Assumindo que o campo 'ocupado' é retornado pela API
                    const cardClass = isOcupado ? 'border-danger bg-light' : '';

                    cardHtml += `
                        <div class="card mb-3 ${cardClass}" style="width: 100%; max-width: 300px;">
                            <img src="${imageUrl}" class="card-img-top visualizar-imagem" data-imagem="${imageUrl}" alt="Imagem do movimento" style="height: 180px; object-fit: cover; cursor: pointer;">
                            <div class="card-body">
                                <h5 class="card-title">ID da Vaga: ${latestMovimento.fk_vacancie}</h5>
                                <p class="card-text">Placa: ${placaText}</p>
                                <p class="card-text"><small class="text-muted">Registrado em: ${createdAtFormatted}</small></p>
                                <a href="../cameras/historical?id=${latestMovimento.fk_vacancie}" class="btn btn-primary mt-2">Ver Histórico Completo</a>
                            </div>
                        </div>
                    `;
                    $('#searchResultsBody').html(cardHtml);
                    $('#searchResultsModal').modal('show'); // Abre o modal com o resultado
                } else {
                    showToast(`Placa <b>${placa}</b> não localizada ou sem movimentos recentes.`, 'danger');
                }
            }).fail(function() {
                showToast('Erro ao comunicar com o servidor. Tente novamente.', 'danger');
            });
        });
    </script>


</body>


</html>
