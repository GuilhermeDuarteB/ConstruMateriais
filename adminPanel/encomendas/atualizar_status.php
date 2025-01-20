<?php
session_start();
include '../../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venda_id']) && isset($_POST['status'])) {
    try {
        $vendaId = intval($_POST['venda_id']);
        $status = $_POST['status'];
        
        // Validar status permitidos
        $statusPermitidos = ['Pendente', 'Confirmado', 'Enviado', 'Entregue', 'Cancelado'];
        if (!in_array($status, $statusPermitidos)) {
            throw new Exception('Status inválido');
        }
        
        $stmt = $conn->prepare("UPDATE Vendas SET Status = ? WHERE VendaID = ?");
        $stmt->execute([$status, $vendaId]);
        
        echo json_encode(['success' => true]);
    } catch(Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} 