<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

session_start();
include '../../connection.php';

// Buscar categorias disponíveis
try {
    $stmt = $conn->query("SELECT CategoriaID, Nome FROM Categorias ORDER BY Nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao carregar categorias: " . $e->getMessage();
}

// Array com todas as características em português correto
$caracteristicas = [
    1 => 'Família de Produto',
    2 => 'Uso Principal',
    3 => 'Tipo de Produto',
    4 => 'Tamanho/Dimensões',
    5 => 'Cor/Acabamento',
    6 => 'Classe de Resistente a Manchas',
    7 => 'Nível de Resistência ao Escorregamento',
    8 => 'Certificação Ecológica',
    9 => 'Resistência à Água',
    10 => 'Materiais Compostos ou Naturais',
    11 => 'Resistência ao Frio/Calor',
    12 => 'Facilidade de Instalação',
    13 => 'Peso por Unidade',
    14 => 'Origem/País de Fabricação',
    15 => 'Durabilidade',
    16 => 'Resistência a Raios UV',
    17 => 'Embalagem',
    18 => 'Aplicação',
    19 => 'Instruções de Manutenção',
    20 => 'Capacidade de Carga',
    21 => 'Recomendações de Uso',
    22 => 'Resistência a Produtos Químicos',
    23 => 'Acabamento',
    24 => 'Destino para o produto',
    25 => 'Tipo de aplicação',
    26 => 'Uso do produto',
    27 => 'Altura da onda',
    28 => 'Comprimento total',
    29 => 'Espessura',
    30 => 'Largura total',
    31 => 'Local de uso',
    32 => 'Consistência / aspeto',
    33 => 'Material principal',
    34 => 'Marca do produto'
];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto - Construmateriais</title>
    <link rel="stylesheet" href="produtos.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            <h2>Adicionar Novo Produto</h2>

            <form class="produto-form" method="POST" action="processar_produto.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome">Nome do Produto*</label>
                    <input type="text" id="nome" name="nome" required>
                </div>

                <div class="form-group">
                    <label for="categoria">Categoria*</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['CategoriaID']; ?>">
                                <?php echo htmlspecialchars($categoria['Nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="preco">Preço Unitário*</label>
                        <input type="number" id="preco" name="preco" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="quantidade">Quantidade em Stock*</label>
                        <input type="number" id="quantidade" name="quantidade" required>
                    </div>
                </div>

                <!-- Seção de características -->
                <div class="caracteristicas-section">
                    <h3>Características do Produto</h3>
                    <div class="caracteristicas-grid">
                        <?php
                        try {
                            // Buscar características
                            $stmt = $conn->query("SELECT CaracteristicaID, Nome FROM Caracteristicas ORDER BY Nome");
                            $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach($caracteristicas as $caracteristica): ?>
                                <div class="form-group">
                                    <label for="caract_<?php echo $caracteristica['CaracteristicaID']; ?>">
                                        <?php echo htmlspecialchars($caracteristica['Nome']); ?>
                                    </label>
                                    <input type="text" 
                                           id="caract_<?php echo $caracteristica['CaracteristicaID']; ?>" 
                                           name="caracteristicas[<?php echo $caracteristica['CaracteristicaID']; ?>]" 
                                           class="caracteristica-input"
                                           placeholder="Digite o valor">
                                </div>
                            <?php endforeach;
                        } catch(PDOException $e) {
                            echo '<div class="erro">Erro ao carregar características: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="form-group fotos-container">
                    <div class="foto-upload">
                        <label for="foto1">Foto Principal*</label>
                        <input type="file" id="foto1" name="foto1" accept="image/*" required>
                        <div class="preview" id="preview1"></div>
                    </div>

                    <div class="foto-upload">
                        <label for="foto2">Foto Adicional 1</label>
                        <input type="file" id="foto2" name="foto2" accept="image/*">
                        <div class="preview" id="preview2"></div>
                    </div>

                    <div class="foto-upload">
                        <label for="foto3">Foto Adicional 2</label>
                        <input type="file" id="foto3" name="foto3" accept="image/*">
                        <div class="preview" id="preview3"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="submit" class="btn-submit">Adicionar Produto</button>
                    <button type="reset" class="btn-reset">Limpar Formulário</button>
                </div>
            </form>
        </main>
    </div>

    <script src="script.js"></script>
    <script>
    document.querySelector('form').addEventListener('submit', function(e) {
        // e.preventDefault(); // Descomente para testar sem enviar
        console.log('Form data:', new FormData(this));
        
        // Log dos valores das características
        const caracteristicas = {};
        document.querySelectorAll('.caracteristica-input').forEach(input => {
            if (input.value) {
                caracteristicas[input.name] = input.value;
            }
        });
        console.log('Características preenchidas:', caracteristicas);
    });
    </script>
</body>
</html>