<?php
session_start();
include '../../connection.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="shortcut icon" type="x-icon" href="../../logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - Construmateriais</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="categorias.css">
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
            <h2>Gerenciar Categorias</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="mensagem mensagem-sucesso">
                    <?php
                    switch($_GET['success']) {
                        case 1:
                            echo "Categoria adicionada com sucesso!";
                            break;
                        case 2:
                            echo "Categoria excluída com sucesso!";
                            break;
                        case 3:
                            echo "Categoria atualizada com sucesso!";
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="mensagem mensagem-erro">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <div class="categoria-container">
                <form action="processar_categoria.php" method="POST" class="form-categoria">
                    <div class="form-grupo">
                        <label for="nome">Nome da Categoria</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="form-grupo">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primario">Adicionar Categoria</button>
                </form>
            </div>

            <div class="categoria-container">
                <h3>Categorias Existentes</h3>
                <div class="tabela-container">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $conn->query("SELECT CategoriaID, Nome, Descricao FROM Categorias");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['CategoriaID']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Nome']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Descricao']) . "</td>";
                                    echo "<td class='acoes'>
                                            <a href='editar_categoria.php?id=" . $row['CategoriaID'] . "' class='btn btn-secundario'>Editar</a>
                                            <a href='excluir_categoria.php?id=" . $row['CategoriaID'] . "' class='btn btn-perigo' onclick='return confirm(\"Tem certeza que deseja excluir esta categoria?\")'>Excluir</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='4'>Erro ao listar categorias: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 