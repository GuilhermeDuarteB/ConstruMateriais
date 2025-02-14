<?php
session_start();
include '../connection.php';

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
    <title>Registo | Construmateriais</title>
    <link rel="stylesheet" href="registo.css">
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="../index.php">
                        <img src="../logo.png" alt="Construmateriais">
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <div class="registro-container">
                <h1>Criar Conta</h1>
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="mensagem erro">
                        <?php
                        switch($_GET['error']) {
                            case 'email_existe':
                                echo "Este email já está registrado.";
                                break;
                            case 'usuario_existe':
                                echo "Este nome de utilizador já está em uso.";
                                break;
                            case 'erro_sistema':
                                echo "Ocorreu um erro no sistema. Tente novamente.";
                                break;
                            default:
                                echo "Ocorreu um erro. Tente novamente.";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <form action="processar_registro.php" method="POST" class="form-registro">
                    <!-- Coluna Esquerda -->
                    <div class="form-coluna">
                        <div class="form-grupo">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>

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
                    </div>

                    <!-- Coluna Direita -->
                    <div class="form-coluna">
                        <div class="form-grupo">
                            <label for="nomeUtilizador">Nome de Utilizador</label>
                            <input type="text" id="nomeUtilizador" name="nomeUtilizador" required>
                        </div>

                        <div class="form-grupo">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" pattern="[0-9]{9}" required>
                        </div>

                        <div class="form-grupo">
                            <label for="morada">Morada</label>
                            <input type="text" id="morada" name="morada" required>
                        </div>
                    </div>

                    <!-- Botão de registro movido para fora das colunas mas ainda dentro do form -->
                    <div class="form-acoes">
                        <button type="submit" class="btn-registro">Criar Conta</button>
                    </div>
                </form>

                <!-- Seção de ações fora do form -->
                <div class="form-acoes">
                    <div class="login-link">
                        Já tem uma conta? <a href="../login/login.php">Faça Login</a>
                    </div>
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