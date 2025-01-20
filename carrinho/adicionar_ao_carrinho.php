<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_id'] === 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, faça login para adicionar produtos ao carrinho.',
        'redirect' => '../login/login.php'
    ]);
    exit;
}

// Verifica se recebeu os dados necessários
if (!isset($_POST['produtoId']) || !isset($_POST['quantidade'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos.'
    ]);
    exit;
}

$clienteId = $_SESSION['cliente_id'];
$produtoId = (int)$_POST['produtoId'];
$quantidade = (int)$_POST['quantidade'];

try {
    // Primeiro, verifica se o produto já existe no carrinho do cliente
    $stmt = $conn->prepare("SELECT CarrinhoID, Quantidade FROM Carrinho WHERE ClienteID = ? AND ProdutoID = ?");
    $stmt->execute([$clienteId, $produtoId]);
    $itemExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($itemExistente) {
        // Atualiza a quantidade se o produto já existe
        $novaQuantidade = $itemExistente['Quantidade'] + $quantidade;
        $stmt = $conn->prepare("UPDATE Carrinho SET Quantidade = ?, DataAdicao = GETDATE() WHERE CarrinhoID = ?");
        $stmt->execute([$novaQuantidade, $itemExistente['CarrinhoID']]);
    } else {
        // Insere novo item no carrinho
        $stmt = $conn->prepare("INSERT INTO Carrinho (ClienteID, ProdutoID, Quantidade) VALUES (?, ?, ?)");
        $stmt->execute([$clienteId, $produtoId, $quantidade]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Produto adicionado ao carrinho com sucesso!'
    ]);

} catch(PDOException $e) {
    error_log("Erro ao adicionar ao carrinho: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Ocorreu um erro ao adicionar o produto ao carrinho.'
    ]);
}
?>