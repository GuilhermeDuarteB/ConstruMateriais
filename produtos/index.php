<?php
session_start();
include '../connection.php';

// Configurações de filtro e ordenação
$categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$ordem = isset($_GET['ordem']) ? $_GET['ordem'] : 'nome_asc';
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$preco_min = isset($_GET['preco_min']) ? (float)$_GET['preco_min'] : 0;
$preco_max = isset($_GET['preco_max']) ? (float)$_GET['preco_max'] : 99999;

// Buscar categorias para o menu
try {
    $stmt = $conn->query("SELECT CategoriaID, Nome FROM Categorias ORDER BY Nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $categorias = [];
}

// Construir query base
$query = "SELECT p.*, c.Nome as CategoriaNome 
          FROM Produtos p 
          LEFT JOIN Categorias c ON p.CategoriaID = c.CategoriaID 
          WHERE p.Status = 1";

$params = [];

// Adicionar busca à query
if (!empty($busca)) {
    $termos = explode(' ', trim($busca));
    $condicoes = [];
    
    foreach ($termos as $idx => $termo) {
        $param = ":busca{$idx}";
        $condicoes[] = "p.Nome LIKE {$param}";
        $params[$param] = '%' . $termo . '%';
    }
    
    if (!empty($condicoes)) {
        $query .= " AND (" . implode(" OR ", $condicoes) . ")";
    }
}

// Adicionar filtro de preço
if ($preco_min > 0) {
    $query .= " AND p.PrecoUnitario >= :preco_min";
    $params[':preco_min'] = $preco_min;
}

if ($preco_max < 99999) {
    $query .= " AND p.PrecoUnitario <= :preco_max";
    $params[':preco_max'] = $preco_max;
}

// Adicionar filtro de categoria
if ($categoria > 0) {
    $query .= " AND p.CategoriaID = :categoria";
    $params[':categoria'] = $categoria;
}

// Adicionar ordenação
switch ($ordem) {
    case 'preco_asc':
        $query .= " ORDER BY p.PrecoUnitario ASC";
        break;
    case 'preco_desc':
        $query .= " ORDER BY p.PrecoUnitario DESC";
        break;
    case 'nome_desc':
        $query .= " ORDER BY p.Nome DESC";
        break;
    default:
        $query .= " ORDER BY p.Nome ASC"; 
}

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $produtos = [];
    // Optional: Log error
    error_log("Erro na busca de produtos: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | Construmateriais</title>
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="produtos.css">
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
                        <input type="text" name="busca" placeholder="O que você procura?" value="<?php echo htmlspecialchars($busca); ?>">
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
                    <a href="../carrinho.php" class="btn-cart">
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
                    <?php foreach($categorias as $cat): ?>
                        <li>
                            <a href="index.php?categoria=<?php echo $cat['CategoriaID']; ?>" 
                               <?php echo $categoria == $cat['CategoriaID'] ? 'class="active"' : ''; ?>>
                                <?php echo htmlspecialchars($cat['Nome']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="../contato/contato.php">Contato</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Conteúdo Principal -->
    <main class="produtos-page">
        <div class="container">
            <div class="produtos-layout">
                <!-- Sidebar com filtros -->
                <aside class="filtros-sidebar">
                    <form action="" method="GET" class="filtros-form">
                        <!-- Busca -->
                        <div class="filtro-grupo">
                            <h3>Buscar</h3>
                            <input type="text" name="busca" value="<?php echo htmlspecialchars($busca); ?>" 
                                   placeholder="Digite um termo...">
                        </div>

                        <!-- Categorias -->
                        <div class="filtro-grupo">
                            <h3>Categorias</h3>
                            <div class="categorias-lista">
                                <label class="categoria-item">
                                    <input type="radio" name="categoria" value="0" 
                                           <?php echo $categoria == 0 ? 'checked' : ''; ?>>
                                    <span>Todas</span>
                                </label>
                                <?php foreach($categorias as $cat): ?>
                                    <label class="categoria-item">
                                        <input type="radio" name="categoria" value="<?php echo $cat['CategoriaID']; ?>" 
                                               <?php echo $categoria == $cat['CategoriaID'] ? 'checked' : ''; ?>>
                                        <span><?php echo htmlspecialchars($cat['Nome']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Preço -->
                        <div class="filtro-grupo">
                            <h3>Preço</h3>
                            <div class="preco-range">
                                <input type="number" name="preco_min" value="<?php echo $preco_min; ?>" 
                                       placeholder="Min" step="0.01">
                                <span>até</span>
                                <input type="number" name="preco_max" value="<?php echo $preco_max; ?>" 
                                       placeholder="Max" step="0.01">
                            </div>
                        </div>

                        <!-- Ordenação -->
                        <div class="filtro-grupo">
                            <h3>Ordenar por</h3>
                            <select name="ordem">
                                <option value="nome_asc" <?php echo $ordem == 'nome_asc' ? 'selected' : ''; ?>>
                                    Nome (A-Z)
                                </option>
                                <option value="nome_desc" <?php echo $ordem == 'nome_desc' ? 'selected' : ''; ?>>
                                    Nome (Z-A)
                                </option>
                                <option value="preco_asc" <?php echo $ordem == 'preco_asc' ? 'selected' : ''; ?>>
                                    Menor Preço
                                </option>
                                <option value="preco_desc" <?php echo $ordem == 'preco_desc' ? 'selected' : ''; ?>>
                                    Maior Preço
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn-filtrar">Aplicar Filtros</button>
                    </form>
                </aside>

                <!-- Lista de produtos -->
                <div class="produtos-lista">
                    <div class="produtos-header">
                        <h1>Produtos</h1>
                        <span class="produtos-count"><?php echo count($produtos); ?> produtos encontrados</span>
                    </div>

                    <div class="produtos-grid">
                        <?php if (empty($produtos)): ?>
                            <div class="sem-produtos">
                                <p>Nenhum produto encontrado com os filtros selecionados.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($produtos as $produto): ?>
                                <div class="produto-card">
                                    <div class="produto-imagem">
                                        <?php
                                        $imagem = base64_encode($produto['Foto']);
                                        echo "<img src='data:image/jpeg;base64,{$imagem}' alt='" . 
                                             htmlspecialchars($produto['Nome']) . "'>";
                                        ?>
                                    </div>
                                    <div class="produto-info">
                                        <h3><?php echo htmlspecialchars($produto['Nome']); ?></h3>
                                        <p class="categoria">
                                            <?php echo htmlspecialchars($produto['CategoriaNome']); ?>
                                        </p>
                                        <p class="preco">
                                            €<?php echo number_format($produto['PrecoUnitario'], 2, ',', '.'); ?>
                                        </p>
                                        <div class="produto-acoes">
                                            <a href="detalhes.php?id=<?php echo $produto['ProdutoID']; ?>" 
                                               class="btn-detalhes">Ver Detalhes</a>
                                            <button onclick="adicionarAoCarrinho(<?php echo $produto['ProdutoID']; ?>)" 
                                                    class="btn-carrinho">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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

    <script src="../script.js"></script>
    <script src="produtos.js"></script>
</body>
</html>