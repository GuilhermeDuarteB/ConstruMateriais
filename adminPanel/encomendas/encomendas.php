<?php
session_start();
include '../../connection.php';

// Buscar todas as vendas com informações do cliente
try {
    // Modificar a consulta para incluir os detalhes dos produtos
    $query = "SELECT v.VendaID, v.DataVenda, v.ValorTotal, v.Status, c.Nome as NomeCliente,
              STRING_AGG(CONCAT(p.Nome, ' (', iv.Quantidade, ' un)'), ', ') as DetalhesProdutos
              FROM Vendas v
              JOIN Clientes c ON v.ClienteID = c.IdCliente
              JOIN ItensVenda iv ON v.VendaID = iv.VendaID
              JOIN Produtos p ON iv.ProdutoID = p.ProdutoID
              GROUP BY v.VendaID, v.DataVenda, v.ValorTotal, v.Status, c.Nome
              ORDER BY v.DataVenda DESC";
              
    $stmt = $conn->query($query);
    $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
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
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Produtos</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendas as $venda): ?>
                            <tr>
                                <td>#<?php echo $venda['VendaID']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($venda['DataVenda'])); ?></td>
                                <td><?php echo htmlspecialchars($venda['NomeCliente']); ?></td>
                                <td>
                                    <div class="produtos-lista">
                                        <?php echo htmlspecialchars($venda['DetalhesProdutos']); ?>
                                    </div>
                                </td>
                                <td>€<?php echo number_format($venda['ValorTotal'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($venda['Status']); ?>">
                                        <?php echo $venda['Status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="verDetalhes(<?php echo $venda['VendaID']; ?>)">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="atualizarStatus(<?php echo $venda['VendaID']; ?>)">
                                        <i class="fas fa-sync-alt"></i> Status
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
    <script>
    function verDetalhes(vendaId) {
        fetch(`get_detalhes_venda.php?vendaId=${vendaId}`)
            .then(response => response.json())
            .then(data => {
                const modalContent = `
                    <div class="modal fade" id="detalhesModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detalhes da Venda #${data.venda.VendaID}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="detalhes-venda">
                                        <div class="info-cliente">
                                            <h6>Informações do Cliente</h6>
                                            <p><strong>Nome:</strong> ${data.venda.NomeCliente}</p>
                                            <p><strong>Email:</strong> ${data.venda.Email}</p>
                                            <p><strong>Telefone:</strong> ${data.venda.Telefone}</p>
                                            <p><strong>Morada:</strong> ${data.venda.Morada}</p>
                                        </div>
                                        
                                        <div class="info-venda">
                                            <h6>Informações da Venda</h6>
                                            <p><strong>Data:</strong> ${new Date(data.venda.DataVenda).toLocaleDateString()}</p>
                                            <p><strong>Forma de Pagamento:</strong> ${data.venda.FormaPagamento}</p>
                                            <p><strong>Status:</strong> ${data.venda.Status}</p>
                                        </div>

                                        <div class="produtos-detalhes">
                                            <h6>Produtos</h6>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Produto</th>
                                                        <th>Quantidade</th>
                                                        <th>Preço Unit.</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${data.itens.map(item => `
                                                        <tr>
                                                            <td>${item.Nome}</td>
                                                            <td>${item.Quantidade}</td>
                                                            <td>€${Number(item.PrecoUnitario).toFixed(2)}</td>
                                                            <td>€${Number(item.Subtotal).toFixed(2)}</td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                        <td><strong>€${Number(data.venda.ValorTotal).toFixed(2)}</strong></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remover modal anterior se existir
                const modalAntigo = document.getElementById('detalhesModal');
                if (modalAntigo) {
                    modalAntigo.remove();
                }

                // Adicionar novo modal ao DOM
                document.body.insertAdjacentHTML('beforeend', modalContent);

                // Mostrar o modal
                const modal = new bootstrap.Modal(document.getElementById('detalhesModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar os detalhes da venda');
            });
    }
    </script>
</body>
</html> 