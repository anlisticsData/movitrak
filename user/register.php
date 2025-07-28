<?php
include_once '../includes/db.php';
include_once '../includes/functions.php';
include_once '../includes/dao/CompaniesDAO.php';
include_once '../includes/dao/UsersDAO.php';

$errorMessage = ''; // Variável para armazenar a mensagem de erro

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName = validateInput($_POST['company_name']);
    $userName = validateInput($_POST['user_name']);
    $login = validateInput($_POST['login']);
    $password = validateInput($_POST['password']);
    $role = 'user'; // Ou defina um valor dinâmico conforme necessário
    $linkSite = validateInput($_POST['link_site']);

    // Verificar se o login já existe
    $usersDAO = new UsersDAO();
    $existingUser = $usersDAO->getUserByLogin($login);

    if ($existingUser) {
        $errorMessage = 'Este login já está em uso. Por favor, escolha outro.';
    } else {
        // Processamento do arquivo de logo
        $fileLogoPath = null; // Inicializa o caminho do arquivo

        if (isset($_FILES['file_logo']) && $_FILES['file_logo']['error'] === UPLOAD_ERR_OK) {
            $fileLogo = $_FILES['file_logo'];
            $uploadDir = '../uploads/';
            $uploadFile = $uploadDir . basename($fileLogo['name']);
            
            // Verificar se o arquivo foi movido corretamente
            if (move_uploaded_file($fileLogo['tmp_name'], $uploadFile)) {
                $fileLogoPath = $uploadFile;
            } else {
                $errorMessage = "Erro ao enviar o arquivo de logo.";
            }
        }

        // Verificar se a empresa já existe
        $companiesDAO = new CompaniesDAO();
        $company = $companiesDAO->getCompanyByName($companyName);

        if (!$company) {
            // Criar a empresa
            $companyId = $companiesDAO->createCompany($companyName);
        } else {
            $companyId = $company['id'];
        }

        // Criar o usuário
        if (!$errorMessage) {
            $usersDAO->createUser($companyId, $userName, $login, $password, $role, $linkSite, $fileLogoPath);
            // Redirecionar para o login após a criação
            redirect('login');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">

    <!-- Adicionando animação -->
    <style>
        body {
            background-color: #f7f7f7;
        }

        .register-container {
            max-width: 600px;
            width: 50%;
            margin: 0 auto;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-out;
        }

        .logo {
            width: 150px;
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .alert {
            font-size: 14px;
            text-align: center;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .register-container {
                width: 80%;
                padding: 20px;
            }

            .logo {
                width: 120px;
            }
        }

        @media (max-width: 576px) {
            .register-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="register-container">
            <div class="text-center">
                <img src="../images/logo.png" alt="Logo" class="logo">
            </div>

            <h2 class="text-center mb-4">Criar Nova Conta</h2>

            <!-- Exibir a mensagem de erro se existir -->
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="company_name">Nome da Empresa</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" required>
                </div>
                <div class="form-group">
                    <label for="user_name">Nome do Usuário</label>
                    <input type="text" class="form-control" id="user_name" name="user_name" required>
                </div>
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" class="form-control" id="login" name="login" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="link_site">Link do Site</label>
                    <input type="text" class="form-control" id="link_site" name="link_site">
                </div>
                <div class="form-group">
                    <label for="file_logo">Logo da Empresa</label>
                    <input type="file" class="form-control" id="file_logo" name="file_logo" required>
                </div>

                <button type="submit" class="btn btn-custom btn-block">Criar Conta</button>
            </form>

            <div class="text-center mt-3">
                <a href="login" class="btn btn-link">Já tenho uma conta</a>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
