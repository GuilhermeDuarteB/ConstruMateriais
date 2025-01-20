<?php
session_start();
include '../connection.php';

// Obter ID do produto da URL
$produto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Buscar categorias para o menu
try {
    $stmt = $conn->query("SELECT CategoriaID, Nome FROM Categorias ORDER BY Nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $categorias = [];
}

// Buscar detalhes do produto
try {
    $stmt = $conn->prepare("
        SELECT p.*, c.Nome as CategoriaNome 
        FROM Produtos p 
        LEFT JOIN Categorias c ON p.CategoriaID = c.CategoriaID 
        WHERE p.ProdutoID = :id AND p.Status = 1
    ");
    $stmt->execute([':id' => $produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        header('Location: index.php');
        exit;
    }
} catch(PDOException $e) {
    header('Location: index.php');
    exit;
}

// Buscar produtos relacionados
try {
    $stmt = $conn->prepare("
        SELECT p.*, c.Nome as CategoriaNome 
        FROM Produtos p 
        LEFT JOIN Categorias c ON p.CategoriaID = c.CategoriaID 
        WHERE p.CategoriaID = :categoria_id 
        AND p.ProdutoID != :produto_id 
        AND p.Status = 1 
        LIMIT 4
    ");
    $stmt->execute([
        ':categoria_id' => $produto['CategoriaID'],
        ':produto_id' => $produto_id
    ]);
    $produtos_relacionados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $produtos_relacionados = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['Nome']); ?> | Construmateriais</title>
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="detalhes.css">
    <!-- Slick Carousel -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="logo">
                    <a href="../index.php">
                        <img src="../logo.png" alt="Construmateriais">
                    </a>
                </div>
                <div class="search-bar">
                    <form action="index.php" method="GET">
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
                    <a href="../carrinho/carrinho.php" class="btn-cart">
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
                    <?php foreach($categorias as $categoria): ?>
                        <li>
                            <a href="index.php?id=<?php echo $categoria['CategoriaID']; ?>">
                                <?php echo htmlspecialchars($categoria['Nome']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="../contato/contato.php">Contato</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="produto-detalhes">
        <div class="container">
            <div class="produto-container">
                <div class="produto-imagem">
                    <div class="carrossel">
                        <?php
                        $imagens = [
                            'Foto' => $produto['Foto'],
                            'Foto2' => $produto['Foto2'],
                            'Foto3' => $produto['Foto3']
                        ];

                        foreach ($imagens as $foto) {
                            if (!empty($foto)) {
                                $imagem = base64_encode($foto);
                                echo "<div class='slick-slide-item'>";
                                echo "<img src='data:image/jpeg;base64,{$imagem}' alt='" . htmlspecialchars($produto['Nome']) . "'>";
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="produto-info">
                    <h1><?php echo htmlspecialchars($produto['Nome']); ?></h1>
                    <p class="categoria"><?php echo htmlspecialchars($produto['CategoriaNome']); ?></p>
                    <p class="preco">€<?php echo number_format($produto['PrecoUnitario'], 2, ',', ''); ?></p>
                    <p class="descricao"><?php echo htmlspecialchars($produto['Descricao']); ?></p>
                    <div class="produto-acoes">
                        <div class="quantidade-wrapper">
                            <button class="btn-qty" onclick="decrementarQuantidade()">-</button>
                            <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="100">
                            <button class="btn-qty" onclick="incrementarQuantidade()">+</button>
                        </div>
                        <?php if(isset($_SESSION['cliente_id'])): ?>
                            <button onclick="adicionarAoCarrinho(<?php echo $produto['ProdutoID']; ?>)" class="btn-adicionar">
                                <i class="fas fa-shopping-cart"></i>
                                Adicionar ao Carrinho
                            </button>
                        <?php else: ?>
                            <a href="../login/login.php" class="btn-adicionar">
                                <i class="fas fa-sign-in-alt"></i>
                                Faça login para comprar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($produtos_relacionados)): ?>
            <section class="produtos-relacionados">
                <h2>Produtos Relacionados</h2>
                <div class="produtos-grid">
                    <?php foreach($produtos_relacionados as $prod): ?>
                        <div class="produto-card">
                            <div class="produto-imagem">
                                <?php
                                $imagem = base64_encode($prod['Foto']);
                                echo "<img src='data:image/jpeg;base64,{$imagem}' alt='" . 
                                     htmlspecialchars($prod['Nome']) . "'>";
                                ?>
                            </div>
                            <div class="produto-info">
                                <h3><?php echo htmlspecialchars($prod['Nome']); ?></h3>
                                <p class="preco">
                                    €<?php echo number_format($prod['PrecoUnitario'], 2, ',', '.'); ?>
                                </p>
                                <div class="produto-acoes">
                                    <a href="detalhes.php?id=<?php echo $prod['ProdutoID']; ?>" 
                                       class="btn-detalhes">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </main>


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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.carrossel').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                slidesToScroll: 1,
                adaptiveHeight: true,
                arrows: true,
                autoplay: false,
                cssEase: 'linear',
                prevArrow: '<button type="button" class="slick-prev">Previous</button>',
                nextArrow: '<button type="button" class="slick-next">Next</button>',
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        arrows: true,
                        adaptiveHeight: true
                    }
                }]
            });
        });
    </script>
    <script src="adicionarAoCarrinho.js"></script>
</body>
<script>
function incrementarQuantidade() {
    var input = document.getElementById('quantidade');
    input.value = parseInt(input.value) + 1;
}

function decrementarQuantidade() {
    var input = document.getElementById('quantidade');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>
</html>


