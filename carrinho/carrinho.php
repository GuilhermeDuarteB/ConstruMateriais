<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: login/login.php");
    exit();
}

$clienteId = $_SESSION['cliente_id'];

try {
    $stmt = $conn->prepare("SELECT c.*, p.Nome, p.PrecoUnitario, p.Foto, p.QuantidadeEstoque 
                            FROM Carrinho c
                            JOIN Produtos p ON c.ProdutoID = p.ProdutoID
                            WHERE c.ClienteID = ?");
    $stmt->execute([$clienteId]);
    $itensCarrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT CategoriaID, Nome FROM Categorias ORDER BY Nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

function atualizarQuantidade($conn, $carrinhoId, $quantidade) {
    try {
        $stmt = $conn->prepare("UPDATE Carrinho SET Quantidade = ? WHERE CarrinhoID = ?");
        $stmt->execute([$quantidade, $carrinhoId]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function removerItem($conn, $carrinhoId) {
    try {
        $stmt = $conn->prepare("DELETE FROM Carrinho WHERE CarrinhoID = ?");
        $stmt->execute([$carrinhoId]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['atualizar'])) {
        foreach ($_POST['quantidade'] as $carrinhoId => $quantidade) {
            if (atualizarQuantidade($conn, $carrinhoId, $quantidade)) {
                $mensagem = "Carrinho atualizado com sucesso!";
            } else {
                $mensagem = "Erro ao atualizar o carrinho.";
                break;
            }
        }
    } elseif (isset($_POST['remover'])) {
        $carrinhoId = $_POST['remover'];
        if (removerItem($conn, $carrinhoId)) {
            $mensagem = "Item removido com sucesso!";
        } else {
            $mensagem = "Erro ao remover o item.";
        }
    }
}

$stmt = $conn->prepare("SELECT c.*, p.Nome, p.PrecoUnitario, p.Foto, p.QuantidadeEstoque 
                        FROM Carrinho c
                        JOIN Produtos p ON c.ProdutoID = p.ProdutoID
                        WHERE c.ClienteID = ?");
$stmt->execute([$clienteId]);
$itensCarrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras - Construmateriais</title>
    <link rel="stylesheet" href="carrinho.css">
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="carrinho.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="logo">
                <a href="../index.php">
                    <img src="../logo.png" alt="Construmateriais">
                </a>
            </div>
            <div class="search-bar">
                <form action="produtos/index.php" method="GET">
                    <input type="text" name="busca" placeholder="O que você procura?" required>
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="user-actions">
                <?php if (isset($_SESSION['cliente_id'])): ?>
                    <a href="../dashboardUser/userPrincipal.php" class="btn-user">
                        <i class="fas fa-user"></i>
                        <span>Minha Conta</span>
                    </a>
                <?php else: ?>
                    <a href="../registo/index.php" class="btn-user">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Registo</span>
                    </a>
                    <a href="../login/login.php" class="btn-user">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Entrar</span>
                    </a>
                <?php endif; ?>
                <a href="carrinho.php" class="btn-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Carrinho</span>
                </a>
            </div>
        </div>
    </div>
    <nav class="nav-main">
        <div class="container">
            <ul class="menu">
                <li><a href="../index.php">Início</a></li>
                <?php foreach ($categorias as $categoria): ?>
                    <li>
                        <a href="produtos/index.php?categoria=<?php echo $categoria['CategoriaID']; ?>">
                            <?php echo htmlspecialchars($categoria['Nome']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>

<div class="container">
    <h1>Seu Carrinho de Compras</h1>

    <div class="carrinho-container">
        <?php if (empty($itensCarrinho)): ?>
            <div class="carrinho-vazio">
                <p>Seu carrinho está vazio</p>
                <a href="../produtos/index.php" class="btn">Continuar a Comprar</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itensCarrinho as $item): ?>
                        <tr data-carrinho-id="<?php echo $item['CarrinhoID']; ?>">
                            <td data-label="Produto">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['Foto']); ?>" alt="<?php echo $item['Nome']; ?>" class="produto-imagem">
                                <?php echo $item['Nome']; ?>
                            </td>
                            <td data-label="Preço">€<?php echo number_format($item['PrecoUnitario'], 2, ',', '.'); ?></td>
                            <td data-label="Quantidade">
                                <input type="number" class="quantidade-input" value="<?php echo $item['Quantidade']; ?>" min="1" max="<?php echo $item['QuantidadeEstoque']; ?>" onchange="atualizarQuantidade(<?php echo $item['CarrinhoID']; ?>, this.value)">
                            </td>
                            <td data-label="Subtotal">€<?php echo number_format($item['PrecoUnitario'] * $item['Quantidade'], 2, ',', '.'); ?></td>
                            <td data-label="Ações">
                                <button class="remover-btn" onclick="removerProduto(<?php echo $item['CarrinhoID']; ?>)">Remover</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="acoes-carrinho">
                <a href="../produtos/index.php" class="btn continuar-comprando">Continuar Comprando</a>
                <a href="finalizar_compra.php" class="btn finalizar-compra">Finalizar Compra</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>Sobre Nós</h4>
                <p>A Construmateriais é a sua parceira ideal para materiais de construção com a melhor qualidade.</p>
            </div>
            <div class="footer-col">
                <h4>Contato</h4>
                <ul>
                    <li><i class="fas fa-phone"></i> (123) 456-7890</li>
                    <li><i class="fas fa-envelope"></i> contato@construmateriais.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> Rua Principal, 123</li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Redes Sociais</h4>
                <div class="social-links">
                    <a href="https://www.instagram.com/construmateriais.2024/?igsh=YTR6eWVmZGg3cndm" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Construmateriais. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

<script>
function removerProduto(carrinhoId) {
    if (confirm('Tem certeza que deseja remover este item do carrinho?')) {
        fetch('remover_produto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `carrinhoId=${carrinhoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove a linha da tabela
                const row = document.querySelector(`tr[data-carrinho-id="${carrinhoId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Verifica se o carrinho ficou vazio
                const tbody = document.querySelector('tbody');
                if (!tbody.hasChildNodes()) {
                    location.reload(); // Recarrega para mostrar mensagem de carrinho vazio
                }
                
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Ocorreu um erro ao remover o produto.');
        });
    }
}

function atualizarQuantidade(carrinhoId, quantidade) {
    // Função para atualizar a quantidade do item via AJAX
    console.log(`Atualizar: ${carrinhoId}, Quantidade: ${quantidade}`);
}
</script>

</body>
</html>
