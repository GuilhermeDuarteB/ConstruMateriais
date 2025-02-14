<?php
session_start();
include '../../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'])) {
    try {
        $produtoId = intval($_POST['produto_id']);
        
        // Atualiza o status do produto (inverte o valor atual)
        $query = "UPDATE Produtos SET Status = ~Status WHERE ProdutoID = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$produtoId]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        error_log("Erro ao alterar status do produto: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Requisição inválida']);
}