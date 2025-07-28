<?php
require_once '../includes/db.php';
require_once '../includes/dao/CamerasDAO.php';

// Iniciar a sessão e verificar se o usuário está autenticado
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$camerasDAO = new CamerasDAO($pdo);

// Verificar se o parâmetro 'id' foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../cameras/cameras');
    exit();
}

$camera_id = intval($_GET['id']);

// Carregar a câmera para edição
$camera = $camerasDAO->getCameraById($camera_id, $user_id);
if (!$camera) {
    header('Location: ../cameras/cameras');
    exit();
}

// Processar o envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $conexao_type = $_POST['conexao_type'];
    $conexao = $_POST['conexao'];

 

    $success = $camerasDAO->updateCamera($camera_id, $name, $conexao_type, $conexao);

    if ($success) {
        $_SESSION['success_message'] = 'Câmera atualizada com sucesso!';
        header('Location: ../cameras/cameras');
        exit();
    } else {
        $_SESSION['error_message'] = 'Erro ao atualizar a câmera!';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Câmera</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <!-- Inclui o menu superior -->
        <?php include('../includes/components/header.php'); ?>

        <div class="row">
            <!-- Inclui a sidebar -->
            <?php include('../includes/components/sidebar.php'); ?>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h2>Editar Câmera</h2>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <form action="edit_camera.php?id=<?php echo $camera_id; ?>" method="POST">
                    <div class="form-group">
                        <label for="name">Nome da Câmera</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($camera['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="conexao_type">Tipo de Conexão</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-plug"></i>
                                </span>
                            </div>
                            <select class="form-control" id="conexao_type" name="conexao_type" required>
                                <option value="IP" <?php echo $camera['conexao_type'] == 'IP' ? 'selected' : ''; ?>>IP <i class="fas fa-network-wired"></i></option>
                                <option value="USB" <?php echo $camera['conexao_type'] == 'USB' ? 'selected' : ''; ?>>USB <i class="fas fa-usb"></i></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="conexao">Conexão</label>
                        <input type="text" class="form-control" id="conexao" name="conexao" value="<?php echo htmlspecialchars($camera['conexao']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Atualizar</button>
                    <a href="../cameras/cameras" class="btn btn-secondary">Cancelar</a>
                </form>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>


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
