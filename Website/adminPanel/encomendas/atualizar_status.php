<?php
session_start();
require_once '../../connection.php';

// Garantir que nenhuma saída seja enviada antes do JSON
ob_clean();
header('Content-Type: application/json');

// Verificar erros de conexão com o banco
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Erro de conexão com o banco']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

$venda_id = filter_input(INPUT_POST, 'venda_id', FILTER_SANITIZE_NUMBER_INT);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!$venda_id || !$status) {
    echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE Vendas SET Status = ? WHERE VendaID = ?");
    $result = $stmt->execute([$status, $venda_id]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar status']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
exit; 