<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

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

    // Debug da consulta
    echo "<!-- Produto ID: " . $produto_id . " -->\n";
    echo "<!-- Dados recuperados: " . (!empty($produto) ? 'sim' : 'não') . " -->\n";
} catch(PDOException $e) {
    echo "<!-- Erro na consulta: " . $e->getMessage() . " -->\n";
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

// Debug para verificar os dados
echo "<!-- Debug Info: \n";
echo "Foto 1 size: " . (empty($produto['Foto']) ? 'empty' : strlen($produto['Foto'])) . "\n";
echo "Foto 2 size: " . (empty($produto['Foto2']) ? 'empty' : strlen($produto['Foto2'])) . "\n";
echo "Foto 3 size: " . (empty($produto['Foto3']) ? 'empty' : strlen($produto['Foto3'])) . "\n";
echo "-->";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Slick Carousel -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css"/>
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
            <div class="produto-grid">
                <!-- Coluna da esquerda: Foto -->
                <div class="produto-imagem">
                    <div class="carrossel">
                        <?php
                        // Primeiro, vamos garantir que temos todas as fotos em um array
                        $fotos = [];
                        
                        if (!empty($produto['Foto'])) {
                            echo "<div class='slick-slide-item'>";
                            echo "<img src='data:image/jpeg;base64," . base64_encode($produto['Foto']) . "' " .
                                 "alt='" . htmlspecialchars($produto['Nome']) . " - Imagem Principal' " .
                                 "onerror=\"this.src='../assets/no-image.jpg'\">";
                            echo "</div>";
                        }
                        
                        if (!empty($produto['Foto2'])) {
                            echo "<div class='slick-slide-item'>";
                            echo "<img src='data:image/jpeg;base64," . base64_encode($produto['Foto2']) . "' " .
                                 "alt='" . htmlspecialchars($produto['Nome']) . " - Imagem 2' " .
                                 "onerror=\"this.src='../assets/no-image.jpg'\">";
                            echo "</div>";
                        }
                        
                        if (!empty($produto['Foto3'])) {
                            echo "<div class='slick-slide-item'>";
                            echo "<img src='data:image/jpeg;base64," . base64_encode($produto['Foto3']) . "' " .
                                 "alt='" . htmlspecialchars($produto['Nome']) . " - Imagem 3' " .
                                 "onerror=\"this.src='../assets/no-image.jpg'\">";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Coluna da direita: Informações do produto -->
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

                <!-- Seção inferior: Características -->
                <?php
                try {
                    $stmt = $conn->prepare("
                        SELECT COUNT(*) as total
                        FROM ProdutoCaracteristicas pc
                        WHERE pc.ProdutoID = :produtoId 
                        AND pc.Valor IS NOT NULL 
                        AND pc.Valor != ''
                    ");
                    $stmt->execute([':produtoId' => $produto_id]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result['total'] > 0) {
                ?>
                    <div class="caracteristicas-wrapper">
                        <button class="accordion-btn">
                            <span>Características do Produto</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="accordion-content">
                            <?php
                            $stmt = $conn->prepare("
                                SELECT c.Nome, pc.Valor 
                                FROM ProdutoCaracteristicas pc
                                JOIN Caracteristicas c ON pc.CaracteristicaID = c.CaracteristicaID
                                WHERE pc.ProdutoID = :produtoId 
                                AND pc.Valor IS NOT NULL AND pc.Valor != ''
                                ORDER BY c.Nome
                            ");
                            $stmt->execute([':produtoId' => $produto_id]);
                            $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            echo '<div class="caracteristicas-grid">';
                            foreach ($caracteristicas as $caracteristica) {
                                echo '<div class="caracteristica-item">';
                                echo '<span class="caracteristica-nome">' . htmlspecialchars($caracteristica['Nome']) . ':</span>';
                                echo '<span class="caracteristica-valor">' . htmlspecialchars($caracteristica['Valor']) . '</span>';
                                echo '</div>';
                            }
                            echo '</div>';
                            ?>
                        </div>
                    </div>
                <?php 
                    }
                } catch(PDOException $e) {
                    error_log("Erro ao verificar características: " . $e->getMessage());
                }
                ?>
            </div>

            <!-- Produtos Relacionados -->
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
    <script>
        $(document).ready(function(){
            $('.carrossel').slick({
                dots: true,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                adaptiveHeight: false,
                fade: true,
                cssEase: 'linear',
                autoplay: false,
                prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            arrows: true,
                            dots: true
                        }
                    }
                ]
            });
        });
    </script>
    <script src="adicionarAoCarrinho.js"></script>
    <script src="caracteristicas.js"></script>
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


