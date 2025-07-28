<?php
// Incluindo as dependências e inicializando o banco de dados
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/dao/VagasDAO.php';
require_once '../includes/dao/CamerasDAO.php'; // Certifique-se de incluir CamerasDAO

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$vagasDAO = new VagasDAO($pdo);
$camerasDAO = new CamerasDAO($pdo); // Instanciar CamerasDAO

// Carregar câmeras para associar à vaga
$cameras = $camerasDAO->getAllCameras($user_id); // Método para obter as câmeras

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar os dados enviados pelo formulário
    $name = trim($_POST['name']);
    $fk_camera = (int)$_POST['fk_camera'];
    $settings = $_POST['settings'];
    $setting_areas = $_POST['setting_areas'];
    $state = (int)$_POST['state'];
    $plate = $_POST['plate'];
    $plate_file = $_FILES['plate_file']['name'];
    $active = isset($_POST['active']) ? 1 : 0;

    // Lidar com o upload do arquivo de placa
    if ($plate_file) {
        // Validar o tipo de arquivo da placa (imagem)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['plate_file']['type'], $allowed_types)) {
            $error_message = "Apenas arquivos de imagem (JPG, PNG, GIF) são permitidos.";
        } else {
            // Caminho para salvar o arquivo
            $upload_dir = 'uploads/';
            $plate_file_path = $upload_dir . basename($plate_file);

            // Verificar se o diretório de upload existe
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Cria o diretório caso não exista
            }

            // Mover o arquivo para o diretório de uploads
            if (move_uploaded_file($_FILES['plate_file']['tmp_name'], $plate_file_path)) {
                // Adicionar a vaga no banco de dados
                $vagasDAO->addVaga($name, $fk_camera, $settings, $setting_areas, $state, $plate, $plate_file_path, $active);

                // Redirecionar para a página de vagas após o cadastro
                header('Location: view_vagas.php');
                exit();
            } else {
                $error_message = "Erro ao enviar o arquivo da placa.";
            }
        }
    } else {
        // Se não houver arquivo de placa, adicionar a vaga sem o arquivo
        $vagasDAO->addVaga($name, $fk_camera, $settings, $setting_areas, $state, $plate, '', $active);

        // Redirecionar para a página de vagas após o cadastro
        header('Location: view_vagas.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Vaga</title>
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

            <!-- Conteúdo principal do Dashboard -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 pt-4">
                <h2>Cadastrar Nova Vaga</h2>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form action="add_vaga.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nome da Vaga</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="fk_camera">Câmera</label>
                        <select class="form-control" id="fk_camera" name="fk_camera" required>
                            <option value="">Selecione a Câmera</option>
                            <?php foreach ($cameras as $camera): ?>
                                <option value="<?php echo $camera['id']; ?>"><?php echo htmlspecialchars($camera['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings">Configurações</label>
                        <textarea class="form-control" id="settings" name="settings" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="setting_areas">Áreas de Configuração</label>
                        <textarea class="form-control" id="setting_areas" name="setting_areas" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="state">Estado</label>
                        <select class="form-control" id="state" name="state" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="plate">Placa</label>
                        <input type="text" class="form-control" id="plate" name="plate" required>
                    </div>

                    <div class="form-group">
                        <label for="plate_file">Arquivo da Placa (opcional)</label>
                        <input type="file" class="form-control-file" id="plate_file" name="plate_file">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="active" name="active" checked>
                        <label class="form-check-label" for="active">Ativar Vaga</label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Cadastrar Vaga</button>
                    <a href="../cameras/view_vagas" class="btn btn-secondary mt-3">Cancelar</a>
                
                
                
                 
                
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
