<?php
session_start();
include '../../connection.php';

$categoria = null;
if (isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM Categorias WHERE CategoriaID = ?");
        $stmt->execute([$_GET['id']]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Erro: " . $e->getMessage());
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $conn->prepare("UPDATE Categorias SET Nome = ?, Descricao = ? WHERE CategoriaID = ?");
        $stmt->execute([$_POST['nome'], $_POST['descricao'], $_POST['id']]);
        header("Location: categorias.php?success=3");
        exit();
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoria</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <main class="main-content">
            <h2>Editar Categoria</h2>
            <form method="POST" class="mt-4">
                <input type="hidden" name="id" value="<?php echo $categoria['CategoriaID']; ?>">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($categoria['Nome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao"><?php echo htmlspecialchars($categoria['Descricao']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="categorias.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </main>
    </div>
</body>
</html> 