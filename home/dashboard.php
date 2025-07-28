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

            </main>
        </div>
    </div>

    <!-- SCRIPTS (jQuery, Bootstrap, Chart.js) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Função para buscar a placa
        const placaBuscaInput = document.getElementById('placaBuscaInput');
        const buscarBtn = document.getElementById('buscarPlacaBtn');
        const feedback = document.getElementById('placaBuscaFeedback');

        buscarBtn.addEventListener('click', function() {
            const placa = placaBuscaInput.value.trim().toUpperCase();
            if (!placa) {
                feedback.innerHTML = '<span class="text-danger">Digite uma placa para buscar.</span>';
                return;
            }

            feedback.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Buscando placa...</span>';

            const data = {
                plate: placa, 
                user: "<?php echo $userId;  ?>" 
            };

            const url = "../api/get-plate.php";
            $.post(url, data, function(response) {
                const responseData = JSON.parse(response);
                if (responseData.success) {
                    feedback.innerHTML = `<span class="text-success">Resultado para: <b>${placa}</b></span>`;
                    // Aqui você pode exibir os resultados em cards, por exemplo
                    let cardsHtml = '';
                    responseData.data.forEach(function(movimento) {
                        cardsHtml += `
                            <div class="card mb-3" style="max-width: 300px;">
                                <img src="../${movimento.file_path}" class="card-img-top visualizar-imagem" data-imagem="../${movimento.file_path}" alt="Imagem do movimento">
                                <div class="card-body">
                                    <h5 class="card-title">ID da Vaga: ${movimento.fk_vacancie}</h5>
                                    <p class="card-text">Placa: ${movimento.placa}</p>
                                    <a href="../cameras/historical?id=${movimento.fk_vacancie}" class="btn btn-primary mt-2">Ver Histórico</a>
                                </div>
                            </div>
                        `;
                    });
                    feedback.innerHTML = cardsHtml;
                } else {
                    feedback.innerHTML = `<span class="text-danger">${responseData.message}</span>`;
                }
            });
        });

        // Função para abrir o modal de visualização da imagem
        const imagemElements = document.querySelectorAll('.visualizar-imagem');
        imagemElements.forEach(element => {
            element.addEventListener('click', function() {
                const imagemUrl = this.getAttribute('data-imagem');
                const modalImg = document.getElementById('imagemModalImg');
                modalImg.src = imagemUrl;
                $('#imagemModal').modal('show');
            });
        });
    </script>

</body>

</html>
