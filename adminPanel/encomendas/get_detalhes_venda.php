<?php
session_start();
include '../../connection.php';

if (!isset($_GET['vendaId'])) {
    echo json_encode(['error' => 'ID da venda não fornecido']);
    exit;
}

$vendaId = (int)$_GET['vendaId'];

try {
    // Buscar informações da venda
    $query = "SELECT v.VendaID, v.DataVenda, v.ValorTotal, v.FormaPagamento, v.Status,
                     c.Nome as NomeCliente, c.Email, c.Telefone, c.Morada
              FROM Vendas v
              JOIN Clientes c ON v.ClienteID = c.IdCliente
              WHERE v.VendaID = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$vendaId]);
    $venda = $stmt->fetch(PDO::FETCH_ASSOC);

    // Buscar itens da venda
    $queryItens = "SELECT p.Nome, iv.Quantidade, iv.PrecoUnitario,
                          (iv.Quantidade * iv.PrecoUnitario) as Subtotal
                   FROM ItensVenda iv
                   JOIN Produtos p ON iv.ProdutoID = p.ProdutoID
                   WHERE iv.VendaID = ?";
    
    $stmt = $conn->prepare($queryItens);
    $stmt->execute([$vendaId]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resultado = [
        'venda' => $venda,
        'itens' => $itens
    ];

    echo json_encode($resultado);

} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 