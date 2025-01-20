<?php
session_start();
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $assunto = trim($_POST['assunto']);
    $mensagem = trim($_POST['mensagem']);

    try {
        // Verifica se todos os campos estão preenchidos
        if (empty($nome) || empty($email) || empty($assunto) || empty($mensagem)) {
            throw new Exception("Todos os campos são obrigatórios.");
        }

        // Verifica se o email é válido
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Por favor, insira um email válido.");
        }

        $stmt = $conn->prepare("INSERT INTO Contatos (Nome, Email, Assunto, Mensagem, DataEnvio) VALUES (?, ?, ?, ?, GETDATE())");
        
        if ($stmt->execute([$nome, $email, $assunto, $mensagem])) {
            $mensagemSucesso = "Mensagem enviada com sucesso!";
        } else {
            throw new Exception("Erro ao enviar mensagem.");
        }
    } catch(Exception $e) {
        $mensagemErro = $e->getMessage();
    }
}

// Buscar categorias para o menu (mantendo consistência com o index.php)
try {
    $stmt = $conn->query("SELECT CategoriaID, Nome FROM Categorias ORDER BY Nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $categorias = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato | Construmateriais</title>
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="contato.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header (igual ao index.php) -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="logo">
                    <a href="../index.php">
                        <img src="../logo.png" alt="Construmateriais">
                    </a>
                </div>
                <div class="search-bar">
                    <form action="../produtos/index.php" method="GET">
                        <input type="text" name="busca" placeholder="O que você procura?" required>
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="user-actions">
                    <?php if(isset($_SESSION['cliente_id'])): ?>
                        <a href="../dashboardUser/userPrincipal.php" class="btn-user">
                            <i class="fas fa-user"></i>
                            <span>Minha Conta</span>
                        </a>
                        <a href="../carrinho/carrinho.php" class="btn-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Carrinho</span>
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
                </div>
            </div>
        </div>
        <nav class="nav-main">
            <div class="container">
                <ul class="menu">
                    <li><a href="../index.php">Início</a></li>
                    <?php foreach($categorias as $categoria): ?>
                        <li>
                            <a href="../produtos/index.php?id=<?php echo $categoria['CategoriaID']; ?>">
                                <?php echo htmlspecialchars($categoria['Nome']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="contato.php">Contato</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Conteúdo Principal -->
    <main class="contato-page">
        <div class="container">
            <section class="contato-section">
                <h1>Entre em Contato</h1>
                <p>Preencha o formulário abaixo para enviar sua mensagem</p>

                <?php if(isset($mensagemSucesso)): ?>
                    <div class="mensagem-sucesso"><?php echo $mensagemSucesso; ?></div>
                <?php endif; ?>

                <?php if(isset($mensagemErro)): ?>
                    <div class="mensagem-erro"><?php echo $mensagemErro; ?></div>
                <?php endif; ?>

                <form class="contato-form" method="POST" action="">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="assunto">Assunto</label>
                        <input type="text" id="assunto" name="assunto" required>
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" rows="6" required></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Enviar Mensagem</button>
                </form>

                <div class="contato-info">
                    <h2>Informações de Contato</h2>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <p>(123) 456-7890</p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <p>contato@construmateriais.com</p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Rua Principal, 123</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer (igual ao index.php) -->
    <footer class="footer">
        <!-- ... (mesmo conteúdo do footer do index.php) ... -->
    </footer>
</body>
</html>
