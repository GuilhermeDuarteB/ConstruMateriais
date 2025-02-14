<?php
session_start();
include '../../connection.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo.png">
    <title>Gerenciar Clientes</title>
    <link rel="stylesheet" href="clientes.css">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h2>Gerenciar Clientes</h2>

            <!-- Filtro de pesquisa -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Pesquisar por nome ou email" 
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de clientes -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Morada</th>
                            <th>NIF</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $query = "SELECT * FROM Clientes";
                            $params = [];

                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                $search = $_GET['search'];
                                $query = "SELECT * FROM Clientes WHERE Nome LIKE ? OR Email LIKE ?";
                                $params = ["%$search%", "%$search%"];
                            }

                            $stmt = $conn->prepare($query);
                            $stmt->execute($params);

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['IdCliente']) . "</td>";
                                echo "<td>" . htmlspecialchars(trim($row['Nome'])) . "</td>";
                                echo "<td>" . htmlspecialchars(trim($row['Email'])) . "</td>";
                                echo "<td>" . htmlspecialchars(trim($row['Telefone'])) . "</td>";
                                echo "<td>" . htmlspecialchars(trim($row['Morada'])) . "</td>";
                                echo "<td>" . htmlspecialchars(trim($row['Contribuinte'])) . "</td>";
                                echo "<td>
                                        <button type='button' class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#detalhesModal" . $row['IdCliente'] . "'>
                                            Detalhes
                                        </button>
                                        <a href='excluir_cliente.php?id=" . $row['IdCliente'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Tem certeza que deseja excluir este cliente?\")'>
                                            Excluir
                                        </a>
                                      </td>";
                                echo "</tr>";

                                // Modal para detalhes do cliente
                                echo "<div class='modal fade' id='detalhesModal" . $row['IdCliente'] . "' tabindex='-1'>
                                        <div class='modal-dialog'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title'>Detalhes do Cliente</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                                </div>
                                                <div class='modal-body'>
                                                    <p><strong>Nome:</strong> " . htmlspecialchars(trim($row['Nome'])) . "</p>
                                                    <p><strong>Nome de Utilizador:</strong> " . htmlspecialchars(trim($row['NomeUtilizador'])) . "</p>
                                                    <p><strong>Email:</strong> " . htmlspecialchars(trim($row['Email'])) . "</p>
                                                    <p><strong>Telefone:</strong> " . htmlspecialchars(trim($row['Telefone'])) . "</p>
                                                    <p><strong>Morada:</strong> " . htmlspecialchars(trim($row['Morada'])) . "</p>
                                                    <p><strong>NIF:</strong> " . htmlspecialchars(trim($row['Contribuinte'])) . "</p>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fechar</button>
                                                </div>
                                            </div>
                                        </div>
                                      </div>";
                            }
                        } catch(PDOException $e) {
                            echo "<tr><td colspan='7'>Erro ao listar clientes: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/your-code.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 