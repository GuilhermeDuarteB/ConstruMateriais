<?php
session_start();
include '../../connection.php';

// Buscar categorias disponíveis
try {
    $stmt = $conn->query("SELECT CategoriaID, Nome FROM Categorias ORDER BY Nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao carregar categorias: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto - Construmateriais</title>
    <link rel="stylesheet" href="produtos.css">
    <link rel="stylesheet" href="../style.css">
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

            <form class="produto-form" action="processar_produto.php" method="POST" enctype="multipart/form-data">
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
                        <label for="quantidade">Quantidade em Estoque*</label>
                        <input type="number" id="quantidade" name="quantidade" required>
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
                    <button type="submit" class="btn-submit">Adicionar Produto</button>
                    <button type="reset" class="btn-reset">Limpar Formulário</button>
                </div>
            </form>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>