<?php
session_start();
include '../../connection.php';

try {
    $query = "SELECT p.*, c.Nome as CategoriaNome 
              FROM Produtos p 
              LEFT JOIN Categorias c ON p.CategoriaID = c.CategoriaID 
              ORDER BY p.Nome";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao carregar produtos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo.png">
    <title>Gerenciar Produtos - Construmateriais</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style.css">
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
            <h2>Gerenciar Produtos</h2>
            
            <div class="mb-3">
                <a href="../produtos/produtos.php" class="btn btn-primary">Adicionar Novo Produto</a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><span class="th-icon"><i class="fas fa-hashtag"></i>ID</span></th>
                            <th><span class="th-icon"><i class="fas fa-box"></i>Nome</span></th>
                            <th><span class="th-icon"><i class="fas fa-tag"></i>Categoria</span></th>
                            <th><span class="th-icon"><i class="fas fa-dollar-sign"></i>Preço</span></th>
                            <th><span class="th-icon"><i class="fas fa-warehouse"></i>Stock</span></th>
                            <th><span class="th-icon"><i class="fas fa-circle"></i>Status</span></th>
                            <th><span class="th-icon"><i class="fas fa-cog"></i>Ações</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($produtos as $produto): ?>
                            <tr>
                                <td><?php echo $produto['ProdutoID']; ?></td>
                                <td><?php echo htmlspecialchars($produto['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($produto['CategoriaNome']); ?></td>
                                <td class="preco"><?php echo number_format($produto['PrecoUnitario'], 2, ',', '.'); ?>€</td>
                                <td>
                                    <span class="estoque <?php 
                                        echo $produto['QuantidadeEstoque'] <= 5 ? 'estoque-baixo' : 
                                            ($produto['QuantidadeEstoque'] <= 20 ? 'estoque-medio' : 'estoque-alto'); 
                                    ?>">
                                        <i class="fas fa-circle"></i>
                                        <?php echo $produto['QuantidadeEstoque']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $produto['Status'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $produto['Status'] ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="toggleStatus(<?php echo $produto['ProdutoID']; ?>, <?php echo $produto['Status']; ?>)">
                                        <i class="fas <?php echo $produto['Status'] ? 'fa-toggle-off' : 'fa-toggle-on'; ?>"></i>
                                        <?php echo $produto['Status'] ? 'Desativar' : 'Ativar'; ?>
                                    </button>
                                    <a href="editar_produto.php?id=<?php echo $produto['ProdutoID']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
    function toggleStatus(produtoId, statusAtual) {
        if (confirm('Deseja realmente ' + (statusAtual ? 'desativar' : 'ativar') + ' este produto?')) {
            fetch('toggle_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'produto_id=' + produtoId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao alterar status do produto');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar a requisição');
            });
        }
    }
    </script>
</body>
</html>