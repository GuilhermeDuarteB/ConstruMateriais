<?php
session_start();
include '../../connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM Categorias WHERE CategoriaID = ?");
        $stmt->execute([$id]);
        
        header("Location: categorias.php?success=2");
        exit();
    } catch(PDOException $e) {
        header("Location: categorias.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} 