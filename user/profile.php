<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/dao/UsersDAO.php';

// Iniciar a sessão e verificar se o usuário está autenticado
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obter o usuário logado
$user_id = $_SESSION['user_id'];
$usersDAO = new UsersDAO($pdo);
$user_info = $usersDAO->getUserById($user_id); // Função para buscar os dados do usuário


$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    // Criar diretório com permissão 0755 se não existir
    mkdir($upload_dir, 0755, true);
}



// Verifica se a requisição POST foi feita para atualizar o perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_success = false;

    // Atualizar o nome do usuário
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
        $update_success = $usersDAO->updateUserName($user_id, $name);
    }

    // Atualizar a senha do usuário
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
        $update_success = $usersDAO->updateUserPassword($user_id, $password);
    }

    // Atualizar o avatar do usuário
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        // Validar o arquivo de imagem (extensão e tipo)
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['avatar']['type'];

        if (in_array($file_type, $allowed_types)) {
            // Define o caminho para o arquivo de avatar
            $file_logo = '../uploads/' . basename($_FILES['avatar']['name']);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_logo)) {
                $update_success = $usersDAO->updateUserAvatar($user_id, $file_logo);
            } else {
                $_SESSION['error_message'] = 'Erro ao fazer o upload da imagem.';
            }
        } else {
            $_SESSION['error_message'] = 'Somente arquivos JPG, JPEG ou PNG são permitidos.';
        }
    }

    if ($update_success) {
        $_SESSION['success_message'] = 'Perfil atualizado com sucesso!';
        header('Location:../user/profile');
        exit();
    } else {
        $_SESSION['error_message'] = 'Erro ao atualizar o perfil!';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h2>Editar Perfil</h2>

                <p>
O perfil de usuário define o nível de acesso, permissões e funcionalidades que um usuário pode utilizar dentro do sistema. Ele é atribuído com base nas funções ou responsabilidades do colaborador na organização, garantindo que cada pessoa tenha acesso apenas às informações e operações necessárias para seu trabalho.



            </p>
                

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <!-- Formulário para editar perfil -->
                <form action="../user/profile" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user_info['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="avatar">Avatar</label>
                        <input type="file" class="form-control-file" id="avatar" name="avatar">
                        <small class="form-text text-muted">Deixe em branco para manter o avatar atual.</small>
                        <?php if ($user_info['file_logo']): ?>
                            <img src="<?php echo $user_info['file_logo']; ?>" alt="Avatar" class="img-thumbnail mt-2" width="100">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Nova Senha</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Digite uma nova senha (deixe em branco para manter a atual)">
                    </div>

                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
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
        }, 2000);  // Espera 5000ms (5 segundos) antes de esconder o loader
    });

    // Exibe o loader ao carregar qualquer página
    window.addEventListener('beforeunload', function() {
       
    });
</script>



</body>

</html>
