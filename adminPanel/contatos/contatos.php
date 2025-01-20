<?php
session_start();
include '../../connection.php';

// Inicializar a variável $contatos como array vazio
$contatos = [];

try {
    $query = "SELECT * FROM Contatos ORDER BY DataEnvio DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $contatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao carregar contatos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo.png">
    <title>Mensagens de Contato - Construmateriais</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="contatos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h2>Mensagens de Contato</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    Mensagem excluída com sucesso!
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Assunto</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contatos)): ?>
                            <?php foreach($contatos as $contato): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($contato['DataEnvio'])); ?></td>
                                    <td><?php echo htmlspecialchars($contato['Nome']); ?></td>
                                    <td><?php echo htmlspecialchars($contato['Email']); ?></td>
                                    <td><?php echo htmlspecialchars($contato['Assunto']); ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick="verMensagem(<?php echo $contato['ContatoID']; ?>)">
                                            Ver Mensagem
                                        </button>
                                        <a href="excluir_mensagem.php?id=<?php echo $contato['ContatoID']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Tem certeza que deseja excluir esta mensagem?')">
                                            Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhuma mensagem encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal para exibir a mensagem -->
    <div class="modal fade" id="mensagemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes da Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js"></script>
    <script src="contatos.js"></script>
</body>
</html> 