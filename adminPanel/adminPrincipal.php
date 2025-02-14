<?php
session_start();
include '../connection.php';

// Função para obter contagens com valor padrão
function getCount($conn, $table, $column = '*', $condition = '') {
    try {
        $sql = "SELECT COUNT($column) as total FROM $table $condition";
        $stmt = $conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0; // Retorna 0 se for null
    } catch(PDOException $e) {
        error_log("Erro na contagem: " . $e->getMessage());
        return 0;
    }
}

// Função para obter valor total das vendas
function getVendasTotal($conn) {
    try {
        $sql = "SELECT COALESCE(SUM(ValorTotal), 0) as total FROM Vendas";
        $stmt = $conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return number_format($result['total'], 2, ',', '.') ?? '0,00';
    } catch(PDOException $e) {
        error_log("Erro ao calcular total de vendas: " . $e->getMessage());
        return '0,00';
    }
}

// Obtém as contagens com valor padrão 0
$totalProdutos = getCount($conn, 'Produtos') ?? 0;
$totalCategorias = getCount($conn, 'Categorias') ?? 0;
$totalClientes = getCount($conn, 'Clientes') ?? 0;
$totalVendas = getCount($conn, 'Vendas') ?? 0;
$valorTotalVendas = getVendasTotal($conn);

// Contagem de vendas por status
$vendasPendentes = getCount($conn, 'Vendas', '*', "WHERE [Status] = 'Pendente'") ?? 0;
$vendasConfirmadas = getCount($conn, 'Vendas', '*', "WHERE [Status] = 'Confirmado'") ?? 0;
$vendasEntregues = getCount($conn, 'Vendas', '*', "WHERE [Status] = 'Entregue'") ?? 0;

// Buscar últimas encomendas com tratamento de erro
try {
    $stmt = $conn->query("SELECT TOP 5 v.VendaID, v.DataVenda, v.ValorTotal, v.[Status], c.Nome as NomeCliente 
                         FROM Vendas v 
                         JOIN Clientes c ON v.ClienteID = c.IdCliente 
                         ORDER BY v.DataVenda DESC");
    $ultimasEncomendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Erro ao buscar últimas encomendas: " . $e->getMessage());
    $ultimasEncomendas = [];
}

// Buscar últimas mensagens com tratamento de erro
try {
    $stmt = $conn->query("SELECT TOP 5 * FROM Contatos ORDER BY DataEnvio DESC");
    $ultimasMensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Erro ao buscar últimas mensagens: " . $e->getMessage());
    $ultimasMensagens = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Construmateriais</title>
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
                <li><a href="adminPrincipal.php"><i class="fas fa-home"></i><span>Painel Administrativo</span></a></li>
                <li><a href="categorias/categorias.php"><i class="fas fa-tags"></i><span>Adicionar Categoria</span></a></li>
                <li><a href="produtos/produtos.php"><i class="fas fa-box"></i><span>Adicionar Produto</span></a></li>
                <li><a href="clientes/clientes.php"><i class="fas fa-users"></i><span>Clientes</span></a></li>
                <li><a href="listaProdutos/listar_produtos.php"><i class="fas fa-list"></i><span>Lista de Produtos</span></a></li>
                <li><a href="encomendas/encomendas.php"><i class="fas fa-shopping-cart"></i><span>Encomendas</span></a></li>
                <li><a href="contatos/contatos.php"><i class="fas fa-envelope"></i><span>Mensagens</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header>
                <h1>Painel Administrativo | Construmateriais</h1>
            </header>

            <div class="dashboard-cards">
                <div class="card" id="vendas-card">
                    <h3><i class="fas fa-shopping-cart"></i> Total de Vendas</h3>
                    <p class="number" data-count="<?php echo $totalVendas ?: '0'; ?>"><?php echo $totalVendas ?: '0'; ?></p>
                    <p class="subtitle">Valor Total: €<?php echo $valorTotalVendas; ?></p>
                </div>
                <div class="card" id="produtos-card">
                    <h3><i class="fas fa-box"></i> Produtos</h3>
                    <p class="number" data-count="<?php echo $totalProdutos ?: '0'; ?>"><?php echo $totalProdutos ?: '0'; ?></p>
                </div>
                <div class="card" id="clientes-card">
                    <h3><i class="fas fa-users"></i> Clientes</h3>
                    <p class="number" data-count="<?php echo $totalClientes ?: '0'; ?>"><?php echo $totalClientes ?: '0'; ?></p>
                </div>
            </div>

            <div class="status-cards">
                <div class="status-card pendente">
                    <h3>Pendentes</h3>
                    <p class="number"><?php echo $vendasPendentes; ?></p>
                </div>
                <div class="status-card confirmado">
                    <h3>Confirmadas</h3>
                    <p class="number"><?php echo $vendasConfirmadas; ?></p>
                </div>
                <div class="status-card entregue">
                    <h3>Entregues</h3>
                    <p class="number"><?php echo $vendasEntregues; ?></p>
                </div>
            </div>

            <div class="dashboard-sections">
                <!-- Últimas Encomendas -->
                <div class="section">
                    <div class="section-header">
                        <h2><i class="fas fa-clock"></i> Últimas Encomendas</h2>
                        <a href="encomendas/encomendas.php" class="view-all">Ver Todas</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ultimasEncomendas as $encomenda): ?>
                                    <tr>
                                        <td>#<?php echo $encomenda['VendaID']; ?></td>
                                        <td><?php echo htmlspecialchars($encomenda['NomeCliente']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($encomenda['DataVenda'])); ?></td>
                                        <td>€<?php echo number_format($encomenda['ValorTotal'], 2, ',', '.'); ?></td>
                                        <td><span class="status-badge <?php echo strtolower($encomenda['Status']); ?>">
                                            <?php echo $encomenda['Status']; ?>
                                        </span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Últimas Mensagens -->
                <div class="section">
                    <div class="section-header">
                        <h2><i class="fas fa-envelope"></i> Últimas Mensagens</h2>
                        <a href="contatos/contatos.php" class="view-all">Ver Todas</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Nome</th>
                                    <th>Assunto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ultimasMensagens as $mensagem): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($mensagem['DataEnvio'])); ?></td>
                                        <td><?php echo htmlspecialchars($mensagem['Nome']); ?></td>
                                        <td><?php echo htmlspecialchars($mensagem['Assunto']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://kit.fontawesome.com/your-code.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>
