<?php
session_start();
include '../../connection.php';

if (isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        
        $stmt = $conn->prepare("SELECT * FROM Contatos WHERE ContatoID = ?");
        $stmt->execute([$id]);
        $contato = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($contato) {
            $response = [
                'nome' => htmlspecialchars($contato['Nome']),
                'email' => htmlspecialchars($contato['Email']),
                'assunto' => htmlspecialchars($contato['Assunto']),
                'mensagem' => htmlspecialchars($contato['Mensagem']),
                'data' => date('d/m/Y H:i', strtotime($contato['DataEnvio']))
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Mensagem não encontrada']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} 