<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

session_start();
include '../../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Habilitar exibição de erros
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Log detalhado dos dados recebidos
        error_log("=== INÍCIO DO PROCESSAMENTO ===");
        error_log("POST: " . print_r($_POST, true));
        error_log("FILES: " . print_r($_FILES, true));

        // Validar dados obrigatórios
        if (empty($_POST['nome']) || empty($_POST['categoria']) || empty($_POST['preco']) || empty($_POST['quantidade'])) {
            throw new Exception("Todos os campos obrigatórios devem ser preenchidos");
        }

        // Processar dados do formulário
        $nome = trim($_POST['nome']);
        $categoriaId = intval($_POST['categoria']);
        $descricao = trim($_POST['descricao']);
        $preco = floatval(str_replace(',', '.', $_POST['preco']));
        $quantidade = intval($_POST['quantidade']);

        error_log("Dados processados:");
        error_log("Nome: $nome");
        error_log("Categoria: $categoriaId");
        error_log("Preço: $preco");
        error_log("Quantidade: $quantidade");

        // Verificar foto principal
        if (!isset($_FILES['foto1']) || $_FILES['foto1']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erro no upload da foto principal: " . $_FILES['foto1']['error']);
        }

        // Processar fotos
        $foto1 = file_get_contents($_FILES['foto1']['tmp_name']);
        $foto2 = null;
        $foto3 = null;

        if (isset($_FILES['foto2']) && $_FILES['foto2']['error'] === UPLOAD_ERR_OK) {
            $foto2 = file_get_contents($_FILES['foto2']['tmp_name']);
        }

        if (isset($_FILES['foto3']) && $_FILES['foto3']['error'] === UPLOAD_ERR_OK) {
            $foto3 = file_get_contents($_FILES['foto3']['tmp_name']);
        }

        error_log("Fotos processadas com sucesso");

        // Preparar e executar a query
        if (!$conn) {
            throw new Exception("Conexão não estabelecida");
        }
        
        $query = "INSERT INTO Produtos (CategoriaID, Nome, Descricao, PrecoUnitario, 
                  QuantidadeEstoque, Foto, Foto2, Foto3) 
                  VALUES (?, ?, ?, ?, ?, CONVERT(varbinary(max), ?), 
                  CONVERT(varbinary(max), ?), CONVERT(varbinary(max), ?))";
        
        error_log("Query preparada: " . $query);
        
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query: " . print_r($conn->errorInfo(), true));
        }

        $params = [
            $categoriaId,
            $nome,
            $descricao,
            $preco,
            $quantidade,
            $foto1,
            $foto2,
            $foto3
        ];

        error_log("Executando query com os parâmetros");
        error_log("Parâmetros: " . print_r([
            'categoriaId' => $categoriaId,
            'nome' => $nome,
            'descricao' => $descricao,
            'preco' => $preco,
            'quantidade' => $quantidade,
            'foto1_size' => strlen($foto1),
            'foto2_size' => $foto2 ? strlen($foto2) : 'null',
            'foto3_size' => $foto3 ? strlen($foto3) : 'null'
        ], true));
        
        if ($stmt->execute($params)) {
            $produtoId = $conn->lastInsertId();
            error_log("Produto inserido com sucesso. ID: " . $produtoId);

            // Processar características
            if (isset($_POST['caracteristicas']) && is_array($_POST['caracteristicas'])) {
                error_log("Processando características...");
                error_log("Características recebidas: " . print_r($_POST['caracteristicas'], true));
                
                foreach ($_POST['caracteristicas'] as $caracteristicaId => $valor) {
                    if (!empty($valor)) {
                        try {
                            $query = "INSERT INTO ProdutoCaracteristicas 
                                     (ProdutoID, CaracteristicaID, Valor) 
                                     VALUES (:produtoId, :caracteristicaId, :valor)";
                            
                            $stmtCaract = $conn->prepare($query);
                            $params = [
                                ':produtoId' => $produtoId,
                                ':caracteristicaId' => $caracteristicaId,
                                ':valor' => utf8_encode($valor)
                            ];
                            
                            error_log("Tentando inserir característica:");
                            error_log("ProdutoID: " . $produtoId);
                            error_log("CaracteristicaID: " . $caracteristicaId);
                            error_log("Valor: " . $valor);
                            
                            if ($stmtCaract->execute($params)) {
                                error_log("Característica inserida com sucesso!");
                            } else {
                                error_log("Erro ao inserir característica: " . print_r($stmtCaract->errorInfo(), true));
                            }
                        } catch (Exception $e) {
                            error_log("Erro ao inserir característica: " . $e->getMessage());
                            error_log("Stack trace: " . $e->getTraceAsString());
                        }
                    }
                }
            } else {
                error_log("Nenhuma característica recebida no POST");
            }

            // Redirecionar após sucesso
            header("Location: produtos.php?success=1");
            exit();
        } else {
            error_log("Erro ao inserir produto: " . print_r($stmt->errorInfo(), true));
            header("Location: produtos.php?error=1");
            exit();
        }

    } catch(Exception $e) {
        error_log("Stack trace: " . $e->getTraceAsString());
        error_log("ERRO DETALHADO: " . print_r($e, true));
        header("Location: produtos.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}

error_log("=== FIM DO PROCESSAMENTO ===");
?>