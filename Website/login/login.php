<?php
session_start();

// Se já estiver logado, redireciona para a página principal
if(isset($_SESSION['cliente_id'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Construmateriais</title>
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="logo">
                    <a href="../index.php">
                        <img src="../logo.png" alt="Construmateriais">
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-login">
        <div class="container">
            <div class="login-container">
                <h1>Login</h1>

                <?php if(isset($_GET['error'])): ?>
                    <div class="mensagem erro">
                        <?php
                        switch($_GET['error']) {
                            case 'credenciais':
                                echo "Email ou senha incorretos.";
                                break;
                            case 'required':
                                echo "Por favor, preencha todos os campos.";
                                break;
                            default:
                                echo "Ocorreu um erro. Tente novamente.";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['success']) && $_GET['success'] == 'registro'): ?>
                    <div class="mensagem sucesso">
                        Conta criada com sucesso! Faça login para continuar.
                    </div>
                <?php endif; ?>

                <form action="processar_login.php" method="POST" class="form-login">
                    <div class="form-grupo">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-grupo">
                        <label for="password">Password</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" required>
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>


                    <button type="submit" class="btn-login">Entrar</button>
                </form>

                <div class="registro-link">
                    Não tem uma conta? <a href="../registo/index.php">Criar conta</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordInput = document.querySelector('#password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html> 