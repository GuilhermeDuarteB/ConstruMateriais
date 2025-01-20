<?php
session_start();
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Verificar credenciais do admin
        if ($email === 'admin@admin' && $password === 'admin123') {
            $_SESSION['admin'] = true;
            $_SESSION['cliente_id'] = 'admin';
            $_SESSION['nome'] = 'Administrador';
            header("Location: ../adminPanel/adminPrincipal.php");
            exit();
        }

        // Verificar se os campos estão preenchidos
        if (empty($email) || empty($password)) {
            throw new Exception('required');
        }

        // Buscar usuário pelo email - usando RTRIM para remover espaços em branco
        $stmt = $conn->prepare("SELECT IdCliente, Nome, Email, Password, NomeUtilizador FROM Clientes WHERE RTRIM(Email) = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug (remover em produção)
        if (!$usuario) {
            error_log("Usuário não encontrado para o email: " . $email);
            throw new Exception('credenciais');
        }

        // Verificar se o usuário existe e a senha está correta
        // Removendo espaços em branco da senha armazenada
        if ($usuario && trim($password) === trim($usuario['Password'])) {
            // Criar sessão
            $_SESSION['cliente_id'] = $usuario['IdCliente'];
            $_SESSION['nome'] = trim($usuario['Nome']); // Remove espaços em branco
            $_SESSION['email'] = trim($usuario['Email']); // Remove espaços em branco
            $_SESSION['username'] = trim($usuario['NomeUtilizador']); // Remove espaços em branco

            // Redirecionar para o dashboard
            header("Location: ../dashboardUser/userPrincipal.php");
            exit();
        } else {
            throw new Exception('credenciais');
        }

    } catch(Exception $e) {
        header("Location: login.php?error=" . $e->getMessage());
        exit();
    }
}
?>