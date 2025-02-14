<?php
session_start();
include '../../connection.php';

// Verifica se foi fornecido um ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar_produtos.php');
    exit;
}

$produto_id = intval($_GET['id']);

try {
    // Busca os dados do produto incluindo as imagens
    $query = "SELECT *, 
              CONVERT(VARCHAR(MAX), CAST(Foto AS VARBINARY(MAX)), 2) as Foto64,
              CONVERT(VARCHAR(MAX), CAST(Foto2 AS VARBINARY(MAX)), 2) as Foto64_2,
              CONVERT(VARCHAR(MAX), CAST(Foto3 AS VARBINARY(MAX)), 2) as Foto64_3 
              FROM Produtos WHERE ProdutoID = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        header('Location: listar_produtos.php');
        exit;
    }

    // Busca todas as categorias
    $query_categorias = "SELECT * FROM Categorias ORDER BY Nome";
    $stmt_categorias = $conn->prepare($query_categorias);
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

    // Processa o formulário quando enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        $preco = str_replace(',', '.', $_POST['preco']);
        $estoque = intval($_POST['estoque']);
        $categoria_id = intval($_POST['categoria']);
        $status = isset($_POST['status']) ? 1 : 0;

        $query_update = "UPDATE Produtos SET 
                        Nome = ?, 
                        Descricao = ?, 
                        PrecoUnitario = ?, 
                        QuantidadeEstoque = ?, 
                        CategoriaID = ?,
                        Status = ?,
                        DataModificacao = GETDATE()
                        WHERE ProdutoID = ?";
        
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->execute([$nome, $descricao, $preco, $estoque, $categoria_id, $status, $produto_id]);

        header('Location: listar_produtos.php?success=1');
        exit;
    }

} catch(PDOException $e) {
    $erro = "Erro ao processar a requisição: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo.png">
    <title>Editar Produto - Construmateriais</title>
    <link rel="stylesheet" href="editar_produto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <a href="listar_produtos.php" class="voltar-btn">
                <i class="fas fa-arrow-left"></i>
                Voltar para Lista
            </a>
            <h1>Editar Produto</h1>
        </header>

        <div class="form-container">
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <!-- Coluna 1 -->
                    <div class="form-column">
                        <div class="form-group">
                            <label for="nome">Nome do Produto</label>
                            <input type="text" id="nome" name="nome" 
                                   value="<?php echo htmlspecialchars($produto['Nome']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" rows="4"><?php 
                                echo htmlspecialchars($produto['Descricao']); 
                            ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="preco">Preço (€)</label>
                                <input type="text" id="preco" name="preco" 
                                       value="<?php echo number_format($produto['PrecoUnitario'], 2, ',', '.'); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="estoque">Stock</label>
                                <input type="number" id="estoque" name="estoque" 
                                       value="<?php echo $produto['QuantidadeEstoque']; ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna 2 -->
                    <div class="form-column">
                        <div class="form-group">
                            <label for="categoria">Categoria</label>
                            <select id="categoria" name="categoria" required>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['CategoriaID']; ?>" 
                                        <?php echo $categoria['CategoriaID'] == $produto['CategoriaID'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categoria['Nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status do Produto</label>
                            <div class="status-toggle">
                                <input type="checkbox" id="status" name="status" 
                                       <?php echo $produto['Status'] ? 'checked' : ''; ?>>
                                <label for="status" class="toggle-label">
                                    <span class="status-text">
                                        <?php echo $produto['Status'] ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-item">
                                <label>Data de Criação</label>
                                <p><?php echo date('d/m/Y H:i', strtotime($produto['DataCriacao'])); ?></p>
                            </div>

                            <div class="info-item">
                                <label>Última Modificação</label>
                                <p><?php echo date('d/m/Y H:i', strtotime($produto['DataModificacao'])); ?></p>
                            </div>

                            <div class="info-item">
                                <label>ID do Produto</label>
                                <p>#<?php echo $produto['ProdutoID']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-salvar">
                        <i class="fas fa-save"></i>
                        Salvar Alterações
                    </button>
                    <a href="listar_produtos.php" class="btn-cancelar">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Formatação do preço
        const precoInput = document.getElementById('preco');
        precoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (parseInt(value) / 100).toFixed(2);
            e.target.value = value.replace('.', ',');
        });

        // Atualização do texto do status
        const statusCheckbox = document.getElementById('status');
        const statusText = document.querySelector('.status-text');
        
        statusCheckbox.addEventListener('change', function() {
            statusText.textContent = this.checked ? 'Ativo' : 'Inativo';
        });
    </script>
</body>
</html> 