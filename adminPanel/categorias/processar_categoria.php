<?php
session_start();
include '../../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);

    try {
        $stmt = $conn->prepare("INSERT INTO Categorias (Nome, Descricao) VALUES (?, ?)");
        $stmt->execute([$nome, $descricao]);
        
        header("Location: categorias.php?success=1");
        exit();
    } catch(PDOException $e) {
        header("Location: categorias.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} 