<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_id'] === 'admin') {
    header("Location: ../../login/login.php");
    exit();
}

$clienteID = $_SESSION['cliente_id'];

// Query corrigida para sua estrutura de banco de dados
$sql = "SELECT v.VendaID, v.DataVenda, v.ValorTotal, v.Status 
        FROM Vendas v 
        WHERE v.ClienteID = ? 
        ORDER BY v.DataVenda DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$clienteID]);
$encomendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minhas Encomendas - Construmateriais</title>
    <link rel="shortcut icon" type="x-icon" href="../../logo.png"> 
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
                <li><a href="../userPrincipal.php"><i class="fas fa-home"></i><span>Painel Principal</span></a></li>
                <li><a href="../minhas_encomendas/minhas_encomendas.php"><i class="fas fa-shopping-bag"></i><span>Minhas Encomendas</span></a></li>
                <li><a href="../perfil/perfil.php"><i class="fas fa-user"></i><span>Meu Perfil</span></a></li>
                <li><a href="../../carrinho/carrinho.php"><i class="fas fa-shopping-cart"></i><span>Carrinho</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Sair</span></a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header>
                <h1>Minhas Encomendas</h1>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nº Encomenda</th>
                            <th>Data</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($encomendas as $encomenda): ?>
                            <tr>
                                <td>#<?php echo $encomenda['VendaID']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($encomenda['DataVenda'])); ?></td>
                                <td>€<?php echo number_format($encomenda['ValorTotal'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($encomenda['Status']); ?>">
                                        <?php echo $encomenda['Status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button onclick="reenviarEncomenda(<?php echo $encomenda['VendaID']; ?>)" class="btn-reenviar">
                                        <i class="fas fa-sync-alt"></i> Reenviar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="https://kit.fontawesome.com/your-code.js"></script>
    <script>
    function reenviarEncomenda(vendaId) {
        if (confirm('Deseja reenviar esta encomenda? Os itens serão adicionados ao seu carrinho.')) {
            fetch('reenviar_encomenda.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `vendaId=${vendaId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '../../carrinho/finalizar_compra.php';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao reenviar a encomenda.');
            });
        }
    }
    </script>
</body>
</html> 