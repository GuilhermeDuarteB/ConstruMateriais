<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$clienteId = $_SESSION['cliente_id'];

try {
    // Buscar informações do cliente
    $stmt = $conn->prepare("SELECT * FROM Clientes WHERE IdCliente = ?");
    $stmt->execute([$clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Buscar itens do carrinho
    $stmt = $conn->prepare("SELECT c.*, p.Nome, p.PrecoUnitario 
                           FROM Carrinho c
                           JOIN Produtos p ON c.ProdutoID = p.ProdutoID
                           WHERE c.ClienteID = ?");
    $stmt->execute([$clienteId]);
    $itensCarrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular valor total
    $valorTotal = 0;
    foreach ($itensCarrinho as $item) {
        $valorTotal += $item['PrecoUnitario'] * $item['Quantidade'];
    }

} catch(PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - Construmateriais</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="finalizar_compra.css">
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
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
            </div>
        </div>
    </header>

    <main class="checkout-page">
        <div class="container">
            <div class="page-title">
                <h1><i class="fas fa-shopping-cart"></i> Finalizar Compra</h1>
            </div>
            
            <div class="checkout-container">
                <form id="checkoutForm" action="processar_compra.php" method="POST">
                    <div class="checkout-grid">
                        <div class="checkout-details">
                            <div class="form-section">
                                <h2><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h2>
                                <div class="form-group">
                                    <label for="morada">Morada*:</label>
                                    <input type="text" id="morada" name="morada" value="<?php echo htmlspecialchars($cliente['Morada']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nif">NIF (opcional):</label>
                                    <input type="text" id="nif" name="nif" value="<?php echo htmlspecialchars($cliente['Contribuinte']); ?>" pattern="\d{9}" maxlength="9">
                                </div>
                            </div>

                            <div class="form-section">
                                <h2><i class="fas fa-credit-card"></i> Método de Pagamento</h2>
                                <div class="payment-options">
                                    <div class="payment-option">
                                        <input type="radio" id="mbway" name="pagamento" value="mbway" required>
                                        <label for="mbway"><i class="fas fa-mobile-alt"></i> MB WAY</label>
                                    </div>
                                    <div class="payment-details" id="mbway-details">
                                        <div class="form-group">
                                            <label for="mbway-telefone">Número de Telefone:</label>
                                            <input type="tel" id="mbway-telefone" name="mbway-telefone" pattern="[0-9]{9}" maxlength="9" placeholder="912345678">
                                        </div>
                                    </div>

                                    <div class="payment-option">
                                        <input type="radio" id="multibanco" name="pagamento" value="multibanco">
                                        <label for="multibanco"><i class="fa-brands fa-paypal"></i> Paypal</label>
                                    </div>
                                    <div class="payment-details" id="multibanco-details">
                                        <div class="form-group">
                                            <label for="multibanco-email">Email para receber referência:</label>
                                            <input type="email" id="multibanco-email" name="multibanco-email" placeholder="seu@email.com">
                                        </div>
                                    </div>

                                    <div class="payment-option">
                                        <input type="radio" id="cartao" name="pagamento" value="cartao">
                                        <label for="cartao"><i class="fas fa-credit-card"></i> Cartão de Crédito/Débito</label>
                                    </div>
                                    <div class="payment-details" id="cartao-details">
                                        <div class="form-group">
                                            <label for="cartao-numero">Número do Cartão:</label>
                                            <input type="text" id="cartao-numero" name="cartao-numero" pattern="[0-9]{16}" maxlength="16" placeholder="1234 5678 9012 3456">
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group half">
                                                <label for="cartao-validade">Data de Validade:</label>
                                                <input type="text" id="cartao-validade" name="cartao-validade" placeholder="MM/AA" maxlength="5">
                                            </div>
                                            <div class="form-group half">
                                                <label for="cartao-cvv">CVV:</label>
                                                <input type="text" id="cartao-cvv" name="cartao-cvv" pattern="[0-9]{3}" maxlength="3" placeholder="123">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="cartao-nome">Nome no Cartão:</label>
                                            <input type="text" id="cartao-nome" name="cartao-nome" placeholder="NOME COMO ESTÁ NO CARTÃO">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="order-summary-container">
                            <div class="order-summary">
                                <h2><i class="fas fa-clipboard-list"></i> Resumo do Pedido</h2>
                                <div class="order-items">
                                    <?php foreach ($itensCarrinho as $item): ?>
                                        <div class="order-item">
                                            <div class="item-details">
                                                <span class="item-name"><?php echo htmlspecialchars($item['Nome']); ?></span>
                                                <span class="item-quantity"><?php echo $item['Quantidade']; ?>x</span>
                                            </div>
                                            <span class="item-price">€<?php echo number_format($item['PrecoUnitario'] * $item['Quantidade'], 2, ',', '.'); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="order-total">
                                    <strong>Total:</strong>
                                    <span class="total-price">€<?php echo number_format($valorTotal, 2, ',', '.'); ?></span>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="carrinho.php" class="btn-voltar">
                                    <i class="fas fa-arrow-left"></i> Voltar ao Carrinho
                                </a>
                                <button type="submit" class="btn-confirmar">
                                    <i class="fas fa-check"></i> Confirmar Compra
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
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
document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('input[name="pagamento"]');
    const allDetails = document.querySelectorAll('.payment-details');
    
    function hideAllDetails() {
        allDetails.forEach(detail => {
            detail.style.display = 'none';
            const inputs = detail.querySelectorAll('input');
            inputs.forEach(input => input.required = false);
        });
    }

    function showSelectedDetails(paymentMethod) {
        const details = document.getElementById(`${paymentMethod}-details`);
        if (details) {
            details.style.display = 'block';
            const inputs = details.querySelectorAll('input');
            inputs.forEach(input => input.required = true);
        }
    }

    hideAllDetails(); // Esconde todos inicialmente

    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            hideAllDetails();
            if (this.checked) {
                showSelectedDetails(this.id);
            }
        });
    });
});
</script>
</body>
</html> 