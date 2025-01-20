<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

$clienteID = $_SESSION['cliente_id'];
$morada = $_POST['morada'];
$nif = $_POST['nif'] ?? null;
$metodoPagamento = $_POST['pagamento'];

try {
    $conn->beginTransaction();

    // Calcular valor total
    $stmt = $conn->prepare("SELECT c.*, p.PrecoUnitario 
                           FROM Carrinho c
                           JOIN Produtos p ON c.ProdutoID = p.ProdutoID
                           WHERE c.ClienteID = ?");
    $stmt->execute([$clienteID]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $valorTotal = 0;
    foreach ($itens as $item) {
        $valorTotal += $item['PrecoUnitario'] * $item['Quantidade'];
    }

    // Inserir venda - ajustado para corresponder à estrutura da tabela
    $stmt = $conn->prepare("INSERT INTO Vendas (ClienteID, ValorTotal, FormaPagamento, Status) 
                           VALUES (?, ?, ?, 'Pendente')");
    $stmt->execute([$clienteID, $valorTotal, $metodoPagamento]);
    $vendaID = $conn->lastInsertId();

    // Inserir itens da venda
    $stmt = $conn->prepare("INSERT INTO ItensVenda (VendaID, ProdutoID, Quantidade, PrecoUnitario) 
                           VALUES (?, ?, ?, ?)");
    
    foreach ($itens as $item) {
        $stmt->execute([
            $vendaID,
            $item['ProdutoID'],
            $item['Quantidade'],
            $item['PrecoUnitario']
        ]);
    }

    // Atualizar morada e NIF do cliente se fornecidos
    $updateFields = [];
    $updateParams = [];
    
    if (!empty($morada)) {
        $updateFields[] = "Morada = ?";
        $updateParams[] = $morada;
    }
    if (!empty($nif)) {
        $updateFields[] = "Contribuinte = ?";
        $updateParams[] = $nif;
    }
    
    if (!empty($updateFields)) {
        $updateParams[] = $clienteID;
        $stmt = $conn->prepare("UPDATE Clientes SET " . implode(", ", $updateFields) . " WHERE IdCliente = ?");
        $stmt->execute($updateParams);
    }

    // Limpar carrinho
    $stmt = $conn->prepare("DELETE FROM Carrinho WHERE ClienteID = ?");
    $stmt->execute([$clienteID]);

    $conn->commit();
    
    // Redirecionar para página de sucesso
    header("Location: sucesso.php?venda=" . $vendaID);
    exit();

} catch(PDOException $e) {
    $conn->rollBack();
    error_log("Erro no processamento da compra: " . $e->getMessage());
    header("Location: erro.php");
    exit();
}