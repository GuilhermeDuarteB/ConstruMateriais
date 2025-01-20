<?php
session_start();
include '../../connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Verificar se existem vendas associadas ao cliente
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Vendas WHERE ClienteID = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            header("Location: clientes.php?error=cliente_com_vendas");
            exit();
        }

        // Se não houver vendas, pode excluir o cliente
        $stmt = $conn->prepare("DELETE FROM Clientes WHERE IdCliente = ?");
        $stmt->execute([$id]);
        
        header("Location: clientes.php?success=cliente_excluido");
        exit();
    } catch(PDOException $e) {
        header("Location: clientes.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} 