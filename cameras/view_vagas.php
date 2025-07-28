<?php
// Incluindo as dependências e inicializando o banco de dados
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/dao/VagasDAO.php';

session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o parâmetro 'camera_id' está presente na URL
if (!isset($_GET['camera_id']) || empty($_GET['camera_id'])) {
    header('Location: ../cameras/cameras');
    exit();
}
$camera_id = intval($_GET['camera_id']);  // Garantindo que o camera_id seja um inteiro

$user_id = $_SESSION['user_id'];
$vagasDAO = new VagasDAO($pdo);

// Filtrar dados se o filtro for enviado
$filter = isset($_GET['filter']) ? htmlspecialchars(trim($_GET['filter'])) : '';

// Puxando as vagas associadas ao usuário e aplicando o filtro
$vagas = $vagasDAO->getVagasByCamera($user_id, $filter, $camera_id);

// Verificar se as vagas foram retornadas
if (!$vagas) {
    $total_vagas = 0;
} else {
    $total_vagas = count($vagas);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Vagas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Inclui o menu superior -->
        <?php include('../includes/components/header.php'); ?>

        <div class="row">
            <!-- Inclui a sidebar -->
            <?php include('../includes/components/sidebar.php'); ?>

            <!-- Conteúdo principal do Dashboard -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 pt-4">
                <h2>Visualizar Vagas</h2>

                <!-- Filtro de pesquisa -->
                <form action="view_vagas.php" method="get" class="form-inline mb-3">
                    <input type="text" class="form-control mr-2" name="filter" placeholder="Buscar por nome da vaga" value="<?php echo htmlspecialchars($filter); ?>">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>

                <!-- Caso não haja vagas -->
                <?php if ($total_vagas == 0): ?>
                    <div class="alert alert-warning">Nenhuma vaga encontrada.</div>
                <?php else: ?>
                    <!-- Tabela de Vagas -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome da Vaga</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vagas as $vaga): ?>
                                <tr>
                                    <td><?php echo $vaga['id']; ?></td>
                                    <td><?php echo htmlspecialchars($vaga['name']); ?></td>
                                    <td><?php echo htmlspecialchars($vaga['settings']); ?></td>
                                    <td>
                                        <?php echo ($vaga['state'] == 1) ? 'Ativa' : 'Inativa'; ?>
                                    </td>
                                    <td>
                                        <!-- Botão de Excluir que chama o modal -->
                                        <button class="btn btn-danger btn-sm deleteVagaBtn" data-id="<?php echo $vaga['id']; ?>" data-toggle="modal" data-target="#deleteVagaModal">
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div class="row justify-content-left mt-3">
                    <div class="col-auto">
                        <a href="add_vaga.php" class="btn btn-success">Adicionar Nova Vaga</a>
                    </div>
                    <div class="col-auto">
                        <a href="../cameras/cameras" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para Excluir Vaga -->
    <div class="modal fade" id="deleteVagaModal" tabindex="-1" role="dialog" aria-labelledby="deleteVagaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteVagaModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja excluir esta vaga?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteVagaBtn">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            let vagaIdToDelete = null;

            // Quando o botão de excluir for clicado, armazena o ID da vaga no modal
            $('.deleteVagaBtn').on('click', function() {
                vagaIdToDelete = $(this).data('id'); // Obtém o ID da vaga
            });

            // Quando o botão "Confirmar Excluir" for clicado
            $('#confirmDeleteVagaBtn').on('click', function() {
                if (vagaIdToDelete) {
                    $.ajax({
                        url: '../cameras/delete_vaga', // certifique-se que o caminho está certo
                        type: 'POST',
                        dataType: 'json', // <-- faz o jQuery já interpretar como JSON
                        data: {
                            id: vagaIdToDelete
                        },
                        success: function(data) {
                            if (data.success) {
                                $('button[data-id="' + vagaIdToDelete + '"]').closest('tr').remove();
                              
                               
                            } 
                              window.location.href='../cameras/cameras'
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText); // mostra o erro real
                            alert('Erro ao excluir a vaga. Tente novamente.');
                        }
                    });




                }
            });
        });
    </script>



<script>
     showLoader();  // Mostra o loader quando a navegação entre páginas começar
    // Função para mostrar o loader
    function showLoader() {
        document.getElementById('loader').style.display = 'flex';
    }

    // Função para esconder o loader após o carregamento da página
    function hideLoader() {
        document.getElementById('loader').style.display = 'none';
    }

    // Exibe o loader assim que a página começa a carregar
    window.addEventListener('load', function() {
        setTimeout(() => {
            hideLoader();  // Esconde o loader após 5 segundos
        }, 1000);  // Espera 5000ms (5 segundos) antes de esconder o loader
    });

    // Exibe o loader ao carregar qualquer página
    window.addEventListener('beforeunload', function() {
       
    });
</script>


</body>

</html>