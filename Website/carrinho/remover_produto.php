<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não está logado'
    ]);
    exit;
}

if (!isset($_POST['carrinhoId'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do item não fornecido'
    ]);
    exit;
}

$carrinhoId = (int)$_POST['carrinhoId'];
$clienteId = $_SESSION['cliente_id'];

try {
    // Verifica se o item pertence ao cliente antes de remover
    $stmt = $conn->prepare("DELETE FROM Carrinho WHERE CarrinhoID = ? AND ClienteID = ?");
    $result = $stmt->execute([$carrinhoId, $clienteId]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Produto removido com sucesso'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Não foi possível remover o produto'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao remover o produto'
    ]);
}
?> 