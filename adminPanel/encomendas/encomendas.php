<?php
session_start();
include '../../connection.php';

// Buscar todas as vendas com informações do cliente
try {
    $query = "SELECT v.*, c.Nome as NomeCliente, c.Email 
              FROM Vendas v 
              LEFT JOIN Clientes c ON v.ClienteID = c.IdCliente 
              ORDER BY v.DataVenda DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao carregar encomendas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo.png">
    <title>Gestão de Encomendas - Construmateriais</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="encomendas.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
    <nav class="sidebar">
            <div class="logo">
               <a href="../../index.php"><img src="../../logo.png" alt="Construmateriais"></a>
            </div>
            <ul class="nav-links">
                <li><a href="../adminPrincipal.php"><i class="fas fa-home"></i><span>Painel Administrativo</span></a></li>
                <li><a href="../categorias/categorias.php"><i class="fas fa-tags"></i><span>Adicionar Categoria</span></a></li>
                <li><a href="../produtos/produtos.php"><i class="fas fa-box"></i><span>Adicionar Produto</span></a></li>
                <li><a href="../clientes/clientes.php"><i class="fas fa-users"></i><span>Clientes</span></a></li>
                <li><a href="../listaProdutos/listar_produtos.php"><i class="fas fa-list"></i><span>Lista de Produtos</span></a></li>
                <li><a href="../encomendas/encomendas.php"><i class="fas fa-shopping-cart"></i><span>Encomendas</span></a></li>
                <li><a href="../contatos/contatos.php"><i class="fas fa-envelope"></i><span>Mensagens</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h2>Gestão de Encomendas</h2>

            <!-- Filtros -->
            <div class="filters-section mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="cliente" placeholder="Buscar por cliente">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="status">
                            <option value="">Todos os status</option>
                            <option value="Pendente">Pendente</option>
                            <option value="Confirmado">Confirmado</option>
                            <option value="Enviado">Enviado</option>
                            <option value="Entregue">Entregue</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- Lista de Encomendas -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Valor Total</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($vendas as $venda): ?>
                            <tr>
                                <td>#<?php echo $venda['VendaID']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($venda['NomeCliente']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($venda['Email']); ?></small>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($venda['DataVenda'])); ?></td>
                                <td><?php echo number_format($venda['ValorTotal'], 2, ',', '.'); ?>€</td>
                                <td><?php echo htmlspecialchars($venda['FormaPagamento']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($venda['Status']); ?>">
                                        <?php echo $venda['Status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="verDetalhes(<?php echo $venda['VendaID']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="atualizarStatus(<?php echo $venda['VendaID']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal de Detalhes -->
    <div class="modal fade" id="detalhesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes da Encomenda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Conteúdo será preenchido via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js"></script>
    <script src="encomendas.js"></script>
</body>
</html> 