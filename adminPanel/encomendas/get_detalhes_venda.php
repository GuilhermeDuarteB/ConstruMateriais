<?php
session_start();
include '../../connection.php';

if (isset($_GET['id'])) {
    try {
        $vendaId = intval($_GET['id']);
        
        // Buscar detalhes da venda e cliente
        $query = "SELECT v.*, c.* 
                  FROM Vendas v 
                  LEFT JOIN Clientes c ON v.ClienteID = c.IdCliente 
                  WHERE v.VendaID = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$vendaId]);
        $venda = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($venda) {
            $response = [
                'cliente' => [
                    'nome' => trim($venda['Nome']),
                    'email' => trim($venda['Email']),
                    'telefone' => trim($venda['Telefone']),
                    'morada' => trim($venda['Morada'])
                ],
                'venda' => [
                    'data' => date('d/m/Y H:i', strtotime($venda['DataVenda'])),
                    'valor_total' => number_format($venda['ValorTotal'], 2, ',', '.'),
                    'forma_pagamento' => $venda['FormaPagamento'],
                    'status' => $venda['Status']
                ]
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Venda não encontrada']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} 