<?php
session_start();
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validar e limpar dados
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $nomeUtilizador = filter_input(INPUT_POST, 'nomeUtilizador', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password']; // Senha sem criptografia
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $morada = filter_input(INPUT_POST, 'morada', FILTER_SANITIZE_STRING);

        // Validações adicionais
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('email_invalido');
        }

        if (!preg_match("/^[0-9]{9}$/", $telefone)) {
            throw new Exception('telefone_invalido');
        }

        // Verificar email existente
        $stmt = $conn->prepare("SELECT Email FROM Clientes WHERE Email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception('email_existe');
        }

        // Verificar nome de utilizador existente
        $stmt = $conn->prepare("SELECT NomeUtilizador FROM Clientes WHERE NomeUtilizador = ?");
        $stmt->execute([$nomeUtilizador]);
        if ($stmt->rowCount() > 0) {
            throw new Exception('usuario_existe');
        }

        // Inserir novo cliente com a senha sem criptografia
        $stmt = $conn->prepare("INSERT INTO Clientes (Nome, NomeUtilizador, Password, Email, Telefone, Morada) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $nomeUtilizador, $password, $email, $telefone, $morada]);

        // Redirecionar para login com mensagem de sucesso
        header("Location: ../login/login.php?success=registro");
        exit();

    } catch(Exception $e) {
        $error = $e->getMessage();
        header("Location: index.php?error=" . $error);
        exit();
    }
}
?>