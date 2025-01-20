<?php
session_start();
include '../connection.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_id'] === 'admin') {
    header("Location: ../login/login.php");
    exit();
}

// Função melhorada para obter contagens
function getCount($conn, $status = null) {
    try {
        $clienteID = $_SESSION['cliente_id'];
        $sql = "SELECT COUNT(*) as total FROM Vendas WHERE ClienteID = :clienteID";
        
        if ($status !== null) {
            $sql .= " AND [Status] = :status";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':clienteID', $clienteID, PDO::PARAM_INT);
        
        if ($status !== null) {
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch(PDOException $e) {
        error_log("Erro na contagem: " . $e->getMessage());
        return 0;
    }
}

// Obtém as contagens específicas do usuário
$clienteID = $_SESSION['cliente_id'];

try {
    // Contagem total de encomendas
    $totalEncomendas = $conn->prepare("SELECT COUNT(*) as total FROM Vendas WHERE ClienteID = ?");
    $totalEncomendas->execute([$clienteID]);
    $totalEncomendas = $totalEncomendas->fetch(PDO::FETCH_ASSOC)['total'];

    // Contagem de encomendas pendentes
    $encomendasPendentes = $conn->prepare("SELECT COUNT(*) as total FROM Vendas WHERE ClienteID = ? AND [Status] = 'Pendente'");
    $encomendasPendentes->execute([$clienteID]);
    $encomendasPendentes = $encomendasPendentes->fetch(PDO::FETCH_ASSOC)['total'];

    // Contagem de encomendas concluídas (incluindo Entregue)
    $encomendasConcluidas = $conn->prepare("SELECT COUNT(*) as total FROM Vendas WHERE ClienteID = ? AND ([Status] = 'Concluído' OR [Status] = 'Entregue')");
    $encomendasConcluidas->execute([$clienteID]);
    $encomendasConcluidas = $encomendasConcluidas->fetch(PDO::FETCH_ASSOC)['total'];

    // Debug das contagens
    error_log("Total de encomendas: " . $totalEncomendas);
    error_log("Encomendas pendentes: " . $encomendasPendentes);
    error_log("Encomendas concluídas: " . $encomendasConcluidas);

} catch(PDOException $e) {
    error_log("Erro ao contar encomendas: " . $e->getMessage());
    $totalEncomendas = 0;
    $encomendasPendentes = 0;
    $encomendasConcluidas = 0;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Cliente - Construmateriais</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="logo">
               <a href="../index.php"><img src="../logo.png" alt="Construmateriais"></a>
            </div>
            <ul class="nav-links">
                <li><a href="userPrincipal.php"><span>Painel Principal</span></a></li>
                <li><a href="minhas_encomendas/minhas_encomendas.php"><span>Minhas Encomendas</span></a></li>
                <li><a href="perfil/perfil.php"><span>Meu Perfil</span></a></li>
                <li><a href="../carrinho/carrinho.php"><span>Carrinho</span></a></li>
                <li><a href="logout.php"><span>Sair</span></a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header>
                <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
            </header>

            <div class="dashboard-cards">
                <div class="card" id="total-encomendas">
                    <h3><i class="fas fa-shopping-bag"></i> Total de Encomendas</h3>
                    <p class="number"><?php echo $totalEncomendas; ?></p>
                </div>
                <div class="card" id="encomendas-pendentes">
                    <h3><i class="fas fa-clock"></i> Encomendas Pendentes</h3>
                    <p class="number"><?php echo $encomendasPendentes; ?></p>
                </div>
                <div class="card" id="encomendas-concluidas">
                    <h3><i class="fas fa-check-circle"></i> Encomendas Concluídas</h3>
                    <p class="number"><?php echo $encomendasConcluidas; ?></p>
                </div>
            </div>

            <div class="dashboard-sections">
                <!-- Últimas Encomendas -->
                <div class="section">
                    <div class="section-header">
                        <h2><i class="fas fa-history"></i> Últimas Encomendas</h2>
                        <a href="minhas_encomendas/minhas_encomendas.php" class="view-all">Ver Todas</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nº Encomenda</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $conn->prepare("SELECT TOP 5 VendaID, DataVenda, ValorTotal, [Status] 
                                                          FROM Vendas 
                                                          WHERE ClienteID = ? 
                                                          ORDER BY DataVenda DESC");
                                    $stmt->execute([$clienteID]);
                                    $ultimasEncomendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($ultimasEncomendas as $encomenda) {
                                        $statusClass = strtolower($encomenda['Status']);
                                        echo "<tr>";
                                        echo "<td>#" . $encomenda['VendaID'] . "</td>";
                                        echo "<td>" . date('d/m/Y H:i', strtotime($encomenda['DataVenda'])) . "</td>";
                                        echo "<td>€" . number_format($encomenda['ValorTotal'], 2, ',', '.') . "</td>";
                                        echo "<td><span class='status-badge $statusClass'>" . $encomenda['Status'] . "</span></td>";
                                        echo "</tr>";
                                    }
                                } catch(PDOException $e) {
                                    error_log("Erro ao buscar últimas encomendas: " . $e->getMessage());
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Carrinho Atual -->
                <div class="section">
                    <div class="section-header">
                        <h2><i class="fas fa-shopping-cart"></i> Seu Carrinho</h2>
                        <a href="../carrinho/carrinho.php" class="view-all">Ver Carrinho</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Preço Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $conn->prepare("SELECT c.*, p.Nome, p.PrecoUnitario 
                                                          FROM Carrinho c
                                                          JOIN Produtos p ON c.ProdutoID = p.ProdutoID
                                                          WHERE c.ClienteID = ?");
                                    $stmt->execute([$clienteID]);
                                    $itensCarrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    $totalCarrinho = 0;

                                    foreach ($itensCarrinho as $item) {
                                        $subtotal = $item['Quantidade'] * $item['PrecoUnitario'];
                                        $totalCarrinho += $subtotal;
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($item['Nome']) . "</td>";
                                        echo "<td>" . $item['Quantidade'] . "</td>";
                                        echo "<td>€" . number_format($item['PrecoUnitario'], 2, ',', '.') . "</td>";
                                        echo "<td>€" . number_format($subtotal, 2, ',', '.') . "</td>";
                                        echo "</tr>";
                                    }

                                    if (empty($itensCarrinho)) {
                                        echo "<tr><td colspan='4' class='empty-cart'>Seu carrinho está vazio</td></tr>";
                                    } else {
                                        echo "<tr class='cart-total'>";
                                        echo "<td colspan='3'><strong>Total</strong></td>";
                                        echo "<td><strong>€" . number_format($totalCarrinho, 2, ',', '.') . "</strong></td>";
                                        echo "</tr>";
                                    }
                                } catch(PDOException $e) {
                                    error_log("Erro ao buscar itens do carrinho: " . $e->getMessage());
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 