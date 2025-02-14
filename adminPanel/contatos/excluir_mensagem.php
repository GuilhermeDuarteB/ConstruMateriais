<?php
session_start();
include '../../connection.php';

if (isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        
        $stmt = $conn->prepare("DELETE FROM Contatos WHERE ContatoID = ?");
        $stmt->execute([$id]);
        
        header("Location: contatos.php?success=1");
        exit();
    } catch(PDOException $e) {
        header("Location: contatos.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}