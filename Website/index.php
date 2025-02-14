<?php

session_start();
include 'connection.php';

// Buscar categorias para o menu
try {
    $stmt = $conn->query("SELECT CategoriaID, Nome FROM Categorias ORDER BY Nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $categorias = [];
}

// Buscar produtos em destaque
try {
    $stmt = $conn->query("SELECT TOP 10 p.*, c.Nome as CategoriaNome 
                         FROM Produtos p 
                         LEFT JOIN Categorias c ON p.CategoriaID = c.CategoriaID 
                         WHERE p.Status = 1 
                         ORDER BY p.DataCriacao DESC");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $produtos = [];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial | Construmateriais</title>
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="logo">
                    <a href="index.php">
                        <img src="logo.png" alt="Construmateriais">
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
                    <?php if(isset($_SESSION['cliente_id'])): ?>
                        <?php if($_SESSION['cliente_id'] === 'admin'): ?>
                            <a href="adminPanel/adminPrincipal.php" class="btn-user">
                                <i class="fas fa-user-shield"></i>
                                <span>Painel Admin</span>
                            </a>
                        <?php else: ?>
                            <a href="dashboardUser/userPrincipal.php" class="btn-user">
                                <i class="fas fa-user"></i>
                                <span>Minha Conta</span>
                            </a>
                            <a href="carrinho/carrinho.php" class="btn-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Carrinho</span>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="registo/index.php" class="btn-user">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Registo</span>
                        </a>
                        <a href="login/login.php" class="btn-user">
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
                    <li><a href="index.php">Início</a></li>
                    <?php foreach($categorias as $categoria): ?>
                        <li>
                            <a href="produtos/index.php?categoria=<?php echo $categoria['CategoriaID']; ?>">
                                <?php echo htmlspecialchars($categoria['Nome']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="contato/contato.php">Contato</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Banner Principal -->
    <section class="banner">
        <div class="container">
            <div class="banner-content">
                <h1>Bem-vindo à Construmateriais</h1>
                <p>Sua loja completa de materiais de construção</p>
                <a href="produtos/index.php" class="btn-primary">Ver Produtos</a>
            </div>
        </div>
    </section>

    <!-- Produtos em Destaque -->
    <section class="produtos-destaque">
        <div class="container">
            <h2>Produtos em Destaque</h2>
            <div class="swiper-container">
                <div class="swiper produtos-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach($produtos as $produto): ?>
                            <div class="swiper-slide">
                                <div class="produto-card">
                                    <div class="produto-imagem">
                                        <?php
                                        $imagem = base64_encode($produto['Foto']);
                                        echo "<img src='data:image/jpeg;base64,{$imagem}' alt='" . htmlspecialchars($produto['Nome']) . "'>";
                                        ?>
                                    </div>
                                    <div class="produto-info">
                                        <h3><?php echo htmlspecialchars($produto['Nome']); ?></h3>
                                        <p class="categoria"><?php echo htmlspecialchars($produto['CategoriaNome']); ?></p>
                                        <p class="preco">€<?php echo number_format($produto['PrecoUnitario'], 2, ',', '.'); ?></p>
                                        <div class="produto-acoes">
                                            <a href="produtos/detalhes.php?id=<?php echo $produto['ProdutoID']; ?>" class="btn-detalhes">Ver Detalhes</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categorias em Destaque -->
    <section class="categorias-destaque">
        <div class="container">
            <h2>Nossas Categorias</h2>
            <div class="categorias-grid">
                <?php foreach($categorias as $categoria): ?>
                    <a href="produtos/index.php?categoria=<?php echo $categoria['CategoriaID']; ?>" class="categoria-card">
                        <h3><?php echo htmlspecialchars($categoria['Nome']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Vantagens -->
    <section class="vantagens">
        <div class="container">
            <div class="vantagens-grid">
                <div class="vantagem-item">
                    <i class="fas fa-truck"></i>
                    <h3>Entrega em Todo País</h3>
                    <p>Entregamos em qualquer lugar de Portugal</p>
                </div>
                <div class="vantagem-item">
                    <i class="fas fa-lock"></i>
                    <h3>Compra Segura</h3>
                    <p>Seus dados sempre protegidos</p>
                </div>
                <div class="vantagem-item">
                    <i class="fas fa-headset"></i>
                    <h3>Suporte Dedicado</h3>
                    <p>Atendimento personalizado</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
                        <li><i class="fas fa-envelope"></i> construmateriais@sapo.pt</li>
                        <li><i class="fas fa-map-marker-alt"></i> Rua Quinta do Rego nº14, Salvaterra de Magos</li>
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

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
       const swiper = new Swiper('.produtos-swiper', {
    slidesPerView: 4,
    spaceBetween: 30,
    loop: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
            spaceBetween: 10
        },
        480: {
            slidesPerView: 2,
            spaceBetween: 20
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 20
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 30
        }
    }
});
    </script>
</body>
</html>