<?php
include '../connection.php';

function getCount($conn, $table, $column = '*', $condition = '') {
    try {
        $sql = "SELECT COUNT($column) as total FROM $table $condition";
        $stmt = $conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch(PDOException $e) {
        return 0;
    }
}

function getVendasTotal($conn) {
    try {
        $stmt = $conn->query("SELECT SUM(ValorTotal) as total FROM Vendas");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch(PDOException $e) {
        return 0;
    }
}

$counts = [
    'produtos' => getCount($conn, 'Produtos'),
    'categorias' => getCount($conn, 'Categorias'),
    'clientes' => getCount($conn, 'Clientes'),
    'vendas' => getCount($conn, 'Vendas'),
    'vendas_pendentes' => getCount($conn, 'Vendas', '*', "WHERE [Status] = 'Pendente'"),
    'vendas_confirmadas' => getCount($conn, 'Vendas', '*', "WHERE [Status] = 'Confirmado'"),
    'vendas_entregues' => getCount($conn, 'Vendas', '*', "WHERE [Status] = 'Entregue'"),
    'valor_total_vendas' => getVendasTotal($conn)
];

header('Content-Type: application/json');
echo json_encode($counts); 