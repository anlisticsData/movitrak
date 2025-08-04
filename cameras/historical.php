<?php
require_once '../includes/db.php';
require_once '../includes/dao/MovimentVacanciesDAO.php';
require_once '../includes/functions.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$movDAO = new MovimentVacanciesDAO($pdo);

// === PEGAR ID DA VAGA ===
$vagaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($vagaId <= 0) {
    die("Vaga inválida");
}

// === PAGINAÇÃO ===
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// === PEGAR MOVIMENTOS DA VAGA ===
$totalMovimentos = $movDAO->countMovimentosPorVaga($vagaId);
$totalPages = ceil($totalMovimentos / $limit);

$movimentos = $movDAO->getMovimentosPorVaga($vagaId, $limit, $offset);



?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Movimentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Histórico de Movimentos da Vaga #<?= $vagaId ?></h2>

    <?php if (empty($movimentos)): ?>
        <div class="alert alert-warning">Nenhum movimento registrado para esta vaga.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Placa</th>
                    <th>Status</th>
                    <th>Câmera</th>
                    <th>Imagem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimentos as $mov): ?>
                    <tr>
                        <td><?= (date('d/m/Y H:i', strtotime($mov['created_at']))) ?></td>
                        <td><?= htmlspecialchars(extrairPlacaMercosul($mov['placa']) ?? '---') ?></td>
                        <td>
                            <?= $mov['state'] == 1 
                                ? '<span class="badge badge-danger">Ocupada</span>' 
                                : '<span class="badge badge-success">Livre</span>'; ?>
                        </td>
                        <td><?= htmlspecialchars($mov['camera_name'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($mov['file_path'])): ?>
                                <img src="../<?= $mov['file_path'] ?>" alt="Movimento" style="width:100px; border-radius:5px;">
                            <?php else: ?>
                                ---
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginação -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?id=<?= $vagaId ?>&page=<?= $page - 1 ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?id=<?= $vagaId ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?id=<?= $vagaId ?>&page=<?= $page + 1 ?>">Próximo</a>
                </li>
            </ul>
        </nav>

    <?php endif; ?>

    <a href="javascript:history.back()" class="btn btn-secondary">Voltar</a>

    <!-- Espaço extra no final da página -->
<div style="height: 80px;"></div>


</div>
</body>
</html>
