<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['cliente_id']) || !isset($_GET['venda'])) {
    header("Location: ../index.php");
    exit();
}

$vendaID = (int)$_GET['venda'];
$clienteID = $_SESSION['cliente_id'];

try {
    // Buscar detalhes da venda
    $stmt = $conn->prepare("
        SELECT v.*, c.Nome as NomeCliente, c.Email
        FROM Vendas v
        JOIN Clientes c ON v.ClienteID = c.IdCliente
        WHERE v.VendaID = ? AND v.ClienteID = ?
    ");
    $stmt->execute([$vendaID, $clienteID]);
    $venda = $stmt->fetch(PDO::FETCH_ASSOC);

    // Buscar itens da venda
    $stmt = $conn->prepare("
        SELECT i.*, p.Nome as NomeProduto
        FROM ItensVenda i
        JOIN Produtos p ON i.ProdutoID = p.ProdutoID
        WHERE i.VendaID = ?
    ");
    $stmt->execute([$vendaID]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log("Erro ao buscar detalhes da venda: " . $e->getMessage());
    header("Location: erro.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Realizada com Sucesso - Construmateriais</title>
    <link rel="stylesheet" href="sucesso.css">
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="sucesso-container">
            <div class="sucesso-header">
                <i class="fas fa-check-circle"></i>
                <h1>Compra Realizada com Sucesso!</h1>
                <p>Obrigado pela sua compra, <?php echo htmlspecialchars($venda['NomeCliente']); ?>!</p>
            </div>

            <div class="pedido-conteudo">
                <div class="coluna-esquerda">
                    <div class="pedido-info">
                        <h2>Detalhes do Pedido</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="label">Número do Pedido:</span>
                                <span class="value">#<?php echo $vendaID; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Data:</span>
                                <span class="value"><?php echo date('d/m/Y H:i', strtotime($venda['DataVenda'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Status:</span>
                                <span class="value status-badge <?php echo strtolower($venda['Status']); ?>">
                                    <?php echo $venda['Status']; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Forma de Pagamento:</span>
                                <span class="value"><?php echo $venda['FormaPagamento']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="proximos-passos">
                        <h2>Próximos Passos</h2>
                        <ul>
                            <li><i class="fas fa-envelope"></i> Um e-mail de confirmação foi enviado para <?php echo htmlspecialchars($venda['Email']); ?></li>
                            <li><i class="fas fa-truck"></i> Você receberá atualizações sobre o status do seu pedido</li>
                            <li><i class="fas fa-box"></i> Acompanhe seu pedido na área "Minhas Encomendas"</li>
                        </ul>
                    </div>
                </div>

                <div class="coluna-direita">
                    <div class="itens-pedido">
                        <h2>Itens do Pedido</h2>
                        <div class="itens-lista">
                            <?php foreach ($itens as $item): ?>
                                <div class="item">
                                    <span class="item-nome"><?php echo htmlspecialchars($item['NomeProduto']); ?></span>
                                    <div class="item-detalhes">
                                        <span class="quantidade"><?php echo $item['Quantidade']; ?>x</span>
                                        <span class="preco">€<?php echo number_format($item['PrecoUnitario'], 2, ',', '.'); ?></span>
                                        <span class="subtotal">€<?php echo number_format($item['Quantidade'] * $item['PrecoUnitario'], 2, ',', '.'); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="total">
                            <span class="label">Total:</span>
                            <span class="value">€<?php echo number_format($venda['ValorTotal'], 2, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="acoes">
                <a href="../dashboardUser/minhas_encomendas/minhas_encomendas.php" class="btn btn-primary">
                    <i class="fas fa-box"></i> Minhas Encomendas
                </a>
                <a href="../produtos/index.php" class="btn btn-secondary">
                    <i class="fas fa-shopping-cart"></i> Continuar Comprando
                </a>
            </div>
        </div>
    </div>
</body>
</html> 