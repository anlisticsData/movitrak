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

$movimentos_recentes = $movimentVacanciesDAO->getMovimentosRecentesPorUsuario($userId);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-img-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
        }
        #autoRefreshPanel {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 10px 20px;
            z-index: 1050;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .toast-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 1060;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <h2 class="mt-3">Dashboard</h2>

    <!-- MOVIMENTOS RECENTES EM TABELA -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h4>Movimentos Recentes</h4>
            <?php if (!empty($movimentos_recentes)) : ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID da Vaga</th>
                                <th>Placa</th>
                                <th>Registrado em</th>
                                <th>Status</th>
                                <th>Imagem</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimentos_recentes as $movimento) : ?>
                                <?php
                                $isOcupado = isset($movimento['ocupado']) && $movimento['ocupado'];
                                $imagePath = !empty($movimento['file_path']) ? '../' . htmlspecialchars($movimento['file_path']) : '../assets/img/no-image.png';
                                ?>
                                <tr class="<?= $isOcupado ? 'table-danger' : ''; ?>">
                                    <td><?= $movimento['fk_vacancie']; ?></td>
                                    <td><?= $movimento['placa'] ?? 'N/A'; ?></td>
                                    <td><?= converterParaSaoPaulo(date('d/m/Y H:i', strtotime($movimento['created_at']))); ?></td>
                                    <td><?= $isOcupado ? '<span class="badge badge-danger">OCUPADA</span>' : '<span class="badge badge-success">LIVRE</span>'; ?></td>
                                    <td>
                                        <img src="<?= $imagePath; ?>" class="table-img-thumbnail visualizar-imagem" data-imagem="<?= $imagePath; ?>" alt="Imagem">
                                    </td>
                                    <td>
                                        <a href="../cameras/historical?id=<?= $movimento['fk_vacancie']; ?>" class="btn btn-sm btn-info">Ver Histórico</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-muted">Nenhum movimento recente encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Painel AutoRefresh -->
<div id="autoRefreshPanel">
    <div class="form-check form-check-inline mb-0 mr-3">
        <input class="form-check-input" type="checkbox" id="autoRefreshToggle">
        <label class="form-check-label" for="autoRefreshToggle">Atualização Automática</label>
    </div>
    <span id="autoRefreshStatus" class="text-muted mr-3">Status: Desativado</span>
    <span id="autoRefreshCountdown" class="text-muted">Próximo em: --</span>
</div>

<!-- Modal Imagem -->
<div class="modal fade" id="imagemModal" tabindex="-1" role="dialog" aria-labelledby="imagemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Visualizar Imagem</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="imagemModalImg" class="img-fluid" alt="Imagem">
      </div>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div class="toast-container"></div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let autoRefreshIntervalId = null;
let countdownIntervalId = null;
let countdownSeconds = 60;
let autoRefreshEnabledByUser = false;
let modalOpenCount = 0;

// Atualiza o status e contador na interface
function updateStatusDisplay() {
    document.getElementById('autoRefreshStatus').textContent = 'Status: ' + (autoRefreshEnabledByUser ? 'Ativado' : 'Desativado');
}
function updateCountdownDisplay() {
    document.getElementById('autoRefreshCountdown').textContent = autoRefreshEnabledByUser ? `Próximo em: ${countdownSeconds}s` : 'Próximo em: --';
}
function resetCountdown() {
    countdownSeconds = 60;
    updateCountdownDisplay();
}
function startCountdownTimer() {
    if (countdownIntervalId !== null) return;
    countdownIntervalId = setInterval(() => {
        if (autoRefreshEnabledByUser && modalOpenCount === 0) {
            countdownSeconds--;
            if (countdownSeconds <= 0) {
                location.reload();
            } else {
                updateCountdownDisplay();
            }
        }
    }, 1000);
}
function stopCountdownTimer() {
    clearInterval(countdownIntervalId);
    countdownIntervalId = null;
}

// Toast
function showToast(message, type = 'info') {
    const container = document.querySelector('.toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast bg-' + type + ' text-white';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('data-delay', '3000');
    toast.innerHTML = `
        <div class="toast-header bg-${type} text-white">
            <strong class="mr-auto">${type.toUpperCase()}</strong>
            <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">
                <span>&times;</span>
            </button>
        </div>
        <div class="toast-body">${message}</div>
    `;
    container.appendChild(toast);
    $(toast).toast('show');
    $(toast).on('hidden.bs.toast', () => toast.remove());
}

// Eventos
$(document).ready(function () {
    const toggle = $('#autoRefreshToggle');
    autoRefreshEnabledByUser = localStorage.getItem('autoRefreshEnabled') === 'true';
    toggle.prop('checked', autoRefreshEnabledByUser);
    updateStatusDisplay();
    updateCountdownDisplay();
    if (autoRefreshEnabledByUser) {
        resetCountdown();
        startCountdownTimer();
    }

    toggle.on('change', function () {
        autoRefreshEnabledByUser = $(this).is(':checked');
        localStorage.setItem('autoRefreshEnabled', autoRefreshEnabledByUser);
        updateStatusDisplay();
        if (autoRefreshEnabledByUser) {
            resetCountdown();
            startCountdownTimer();
        } else {
            stopCountdownTimer();
            updateCountdownDisplay();
        }
    });

    $(document).on('show.bs.modal', '.modal', () => modalOpenCount++);
    $(document).on('hidden.bs.modal', '.modal', () => {
        modalOpenCount--;
        if (modalOpenCount === 0 && autoRefreshEnabledByUser) {
            resetCountdown();
            startCountdownTimer();
        }
    });

    // Modal da imagem
    $(document).on('click', '.visualizar-imagem', function () {
        const imagemUrl = $(this).data('imagem');
        $('#imagemModalImg').attr('src', imagemUrl);
        $('#imagemModal').modal('show');
    });
});
</script>
</body>
</html>
