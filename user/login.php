<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';
include_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = validateInput($_POST['login']);
    $password = validateInput($_POST['password']);

    if (loginUser($login, $password)) {
        redirect('../home/dashboard');
    } else {
        $error = "Usuário ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet"> <!-- Seu arquivo de estilos personalizado -->

    <!-- Adicionando animação -->
    <style>
        body {
            background-color: #f7f7f7;
        }

        .login-container {
            max-width: 500px;
            width: 500px;
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

        /* Responsividade */
        @media (max-width: 576px) {
            .login-container {
                padding: 20px;
                width: 90%;
            }

            .logo {
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-container">
            <div class="text-center">
                <img src="../images/logo.png" alt="Logo" class="logo"> <!-- Substitua com o logo da sua empresa -->
            </div>
            
            <h2 class="text-center mb-4">Login</h2>

            <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

            <form method="POST">
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" class="form-control" id="login" name="login" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-custom btn-block">Entrar</button>
            </form>

            <div class="text-center mt-3">
                <a href="register" class="btn btn-link">Criar nova conta</a>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
