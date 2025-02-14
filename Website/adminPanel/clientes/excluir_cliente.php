<?php
session_start();
include '../../connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Iniciar transação
        $conn->beginTransaction();

        // 1. Excluir registros do Carrinho
        $stmt = $conn->prepare("DELETE FROM Carrinho WHERE ClienteID = ?");
        $stmt->execute([$id]);

        // 2. Excluir registros de ItensVenda relacionados às Vendas do cliente
        $stmt = $conn->prepare("DELETE FROM ItensVenda WHERE VendaID IN (SELECT VendaID FROM Vendas WHERE ClienteID = ?)");
        $stmt->execute([$id]);

        // 3. Excluir registros de Vendas
        $stmt = $conn->prepare("DELETE FROM Vendas WHERE ClienteID = ?");
        $stmt->execute([$id]);

        // 4. Excluir endereços do cliente
        $stmt = $conn->prepare("DELETE FROM EnderecosCliente WHERE ClienteID = ?");
        $stmt->execute([$id]);

        // 5. Finalmente, excluir o cliente
        $stmt = $conn->prepare("DELETE FROM Clientes WHERE IdCliente = ?");
        $stmt->execute([$id]);

        // Confirmar transação
        $conn->commit();
        
        header("Location: clientes.php?success=cliente_excluido");
        exit();
    } catch(PDOException $e) {
        // Em caso de erro, desfaz todas as operações
        $conn->rollBack();
        header("Location: clientes.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} 