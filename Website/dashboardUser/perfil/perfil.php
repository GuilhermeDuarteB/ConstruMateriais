<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_id'] === 'admin') {
    header("Location: ../../login/login.php");
    exit();
}

$clienteID = $_SESSION['cliente_id'];
$stmt = $conn->prepare("SELECT * FROM Clientes WHERE IdCliente = ?");
$stmt->execute([$clienteID]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo.png">
    <title>Meu Perfil - Construmateriais</title>
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
                <h1>Meu Perfil</h1>
            </header>

            <div class="profile-container">
                <form action="atualizar_perfil.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" value="<?php echo trim($usuario['Nome']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo trim($usuario['Email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="tel" name="telefone" value="<?php echo trim($usuario['Telefone']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Morada</label>
                        <input type="text" name="morada" value="<?php echo trim($usuario['Morada']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>NIF</label>
                        <input type="text" name="contribuinte" value="<?php echo trim($usuario['Contribuinte']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Nova Senha (deixe em branco para manter a atual)</label>
                        <input type="password" name="nova_senha">
                    </div>

                    <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html> 