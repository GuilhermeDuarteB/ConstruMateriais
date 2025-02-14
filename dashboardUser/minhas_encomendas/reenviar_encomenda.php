<?php
session_start();
include '../../connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não está logado'
    ]);
    exit;
}

if (!isset($_POST['vendaId'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID da venda não fornecido'
    ]);
    exit;
}

$vendaId = (int)$_POST['vendaId'];
$clienteId = $_SESSION['cliente_id'];

try {
    $conn->beginTransaction();

    // Limpar carrinho atual
    $stmt = $conn->prepare("DELETE FROM Carrinho WHERE ClienteID = ?");
    $stmt->execute([$clienteId]);

    // Buscar itens da venda original
    $stmt = $conn->prepare("
        SELECT iv.ProdutoID, iv.Quantidade
        FROM ItensVenda iv
        JOIN Vendas v ON iv.VendaID = v.VendaID
        WHERE v.VendaID = ? AND v.ClienteID = ?
    ");
    $stmt->execute([$vendaId, $clienteId]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Adicionar itens ao carrinho
    $stmt = $conn->prepare("INSERT INTO Carrinho (ClienteID, ProdutoID, Quantidade) VALUES (?, ?, ?)");
    foreach ($itens as $item) {
        $stmt->execute([$clienteId, $item['ProdutoID'], $item['Quantidade']]);
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Itens adicionados ao carrinho com sucesso'
    ]);

} catch(PDOException $e) {
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar o reenvio da encomenda'
    ]);
}
?> 