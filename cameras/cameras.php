<?php
require_once '../includes/db.php';
require_once '../includes/dao/CamerasDAO.php';
require_once '../includes/dao/VagasDAO.php';

// Iniciar a sessão e verificar se o usuário está autenticado
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_camera') {
    // Pega o ID da câmera
    $camera_id = $_POST['camera_id'];

    // Excluir a câmera do banco de dados
    $stmt = $pdo->prepare("DELETE FROM cameras WHERE id = :id");
    $stmt->bindParam(':id', $camera_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Retorna uma resposta de sucesso
        echo json_encode(['success' => true]);
    } else {
        // Retorna uma resposta de erro
        echo json_encode(['success' => false]);
    }

    // Encerra o script
    exit;
}




$user_id = $_SESSION['user_id'];
$camerasDAO = new CamerasDAO($pdo);
$vagasDAO = new VagasDAO($pdo);

// Definir a página e limite para paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;  // Limite de itens por página

// Filtro de pesquisa
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Obter a lista de câmeras com paginação
$cameras = $camerasDAO->getCameras($user_id, $page, $limit, $filter);

// Contar o total de câmeras para a paginação
$total_cameras = $camerasDAO->countCameras($user_id, $filter);
$total_pages = ceil($total_cameras / $limit);
?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Câmeras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilo para truncar o texto de Conexão */
        .conexao-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            cursor: pointer;
        }

        .modal-body {
  word-wrap: break-word;   /* Quebra longas palavras */
  overflow-wrap: break-word; 
  white-space: normal;     /* Permite quebra de linha */
  max-width: 100%;         /* Não ultrapassa o modal */
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h2>Gerenciar Câmeras</h2>

                <p class="text-muted">
                    Gerencie suas câmeras, adicione novas vagas e faça a manutenção do sistema de vigilância.
                </p>

                <!-- Filtro de pesquisa -->
                <form action="../cameras/cameras" method="get" class="form-inline mb-3">
                    <input type="text" class="form-control mr-2" name="filter" placeholder="Buscar por nome ou conexão" value="<?php echo htmlspecialchars($filter); ?>">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>

                <!-- Botão para adicionar nova vaga, caso existam câmeras cadastradas -->
                <?php if ($total_cameras > 0): ?>
                    <a href="../cameras/add_vaga" class="btn btn-success mb-3">Adicionar Nova Vaga</a>
                <?php else: ?>
                    <div class="alert alert-warning mb-3">Nenhuma câmera cadastrada. Adicione uma câmera antes de adicionar vagas.</div>
                <?php endif; ?>

                <!-- Tabela de Câmeras -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Tipo de Conexão</th>
                            <th>Conexão</th>
                            <th>Total de Vagas</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cameras as $camera):
                            // Contar o total de vagas para cada câmera
                            $total_vagas = $vagasDAO->countVagasByCamera($camera['id']);
                        ?>
                            <tr id="cameraRow<?php echo $camera['id']; ?>">
                                <td><?php echo $camera['id']; ?></td>
                                <td><?php echo htmlspecialchars($camera['name']); ?></td>
                                <td><?php echo htmlspecialchars($camera['conexao_type']); ?></td>
                                <td>
                                    <!-- Conexão com texto truncado e link para abrir modal -->
                                    <span class="conexao-text" data-toggle="modal" data-target="#modalConexao<?php echo $camera['id']; ?>">
                                        Conferir
                                    </span>
                                </td>
                                <td><?php echo $total_vagas; ?></td>
                                <td>
                                    <!-- Botões de Ação -->
                                    <a href="edit_camera?id=<?php echo $camera['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="view_vagas?camera_id=<?php echo $camera['id']; ?>" class="btn btn-info btn-sm">Ver Vagas</a>
                                    <?php if ($total_vagas == 0): ?>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?php echo $camera['id']; ?>">Excluir</button>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Modal para mostrar o conteúdo completo da Conexão -->
                            <div class="modal fade" id="modalConexao<?php echo $camera['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?php echo $camera['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel<?php echo $camera['id']; ?>">Detalhes da Conexão</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p><?php echo nl2br(htmlspecialchars($camera['conexao'])); ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal de confirmação de exclusão -->
                            <!-- Modal de confirmação de exclusão -->
                            <div class="modal fade" id="deleteModal<?php echo $camera['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $camera['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel<?php echo $camera['id']; ?>">Confirmar Exclusão</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Tem certeza que deseja excluir a câmera <strong><?php echo htmlspecialchars($camera['name']); ?></strong>? Esta ação não pode ser desfeita.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <!-- Botão de Exclusão com o atributo data-id -->
                                            <button type="button" class="btn btn-danger delete-camera-btn" data-id="<?php echo $camera['id']; ?>" data-toggle="modal" data-target="#deleteModal<?php echo $camera['id']; ?>">Excluir</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Paginação -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>&filter=<?php echo htmlspecialchars($filter); ?>">Anterior</a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&filter=<?php echo htmlspecialchars($filter); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo ($page + 1); ?>&filter=<?php echo htmlspecialchars($filter); ?>">Próximo</a>
                        </li>
                    </ul>
                </nav>

                <!-- Botão para adicionar nova câmera -->
                <a href="../cameras/add_camera" class="btn btn-success mt-3">Adicionar Nova Câmera</a>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Ao clicar no botão de confirmar exclusão
            $(document).on('click', '.delete-camera-btn', function() {


                var cameraId = $(this).data('id'); // Pega o ID da câmera

                 // Envia a requisição AJAX para excluir a câmera
                $.ajax({
                    url: '../cameras/delete_camera', // URL do script de exclusão
                    type: 'POST',
                    data: {
                        id: cameraId
                    },
                    success: function(response) {
                        console.log(response)
                        if (response.success) {
                            // Remove a linha da câmera da tabela
                            $('#cameraRow' + cameraId).remove();
                            $('#deleteModal' + cameraId).modal('hide');
                            
                        } 
                        window.location.href='../cameras/cameras'

                    },
                    error: function() {
                        alert('Erro no servidor. Tente novamente!');
                    }
                });
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