function verDetalhes(vendaId) {
    // Fazer requisição AJAX para buscar detalhes da venda
    fetch(`get_detalhes_venda.php?id=${vendaId}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.querySelector('#detalhesModal .modal-body');
            modalBody.innerHTML = `
                <dl class="order-details">
                    <dt>Cliente:</dt>
                    <dd>${data.cliente.nome}</dd>
                    
                    <dt>Email:</dt>
                    <dd>${data.cliente.email}</dd>
                    
                    <dt>Telefone:</dt>
                    <dd>${data.cliente.telefone}</dd>
                    
                    <dt>Morada:</dt>
                    <dd>${data.cliente.morada}</dd>
                    
                    <dt>Data da Venda:</dt>
                    <dd>${data.venda.data}</dd>
                    
                    <dt>Valor Total:</dt>
                    <dd>${data.venda.valor_total}€</dd>
                    
                    <dt>Forma de Pagamento:</dt>
                    <dd>${data.venda.forma_pagamento}</dd>
                    
                    <dt>Status:</dt>
                    <dd>
                        <span class="status-badge status-${data.venda.status.toLowerCase()}">
                            ${data.venda.status}
                        </span>
                    </dd>
                </dl>
            `;
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('detalhesModal'));
            modal.show();
        })
        .catch(error => console.error('Erro:', error));
}

function atualizarStatus(vendaId) {
    const novoStatus = prompt('Digite o novo status (Pendente, Confirmado, Enviado, Entregue, Cancelado):');
    if (novoStatus) {
        fetch('atualizar_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `venda_id=${vendaId}&status=${novoStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao atualizar status');
            }
        })
        .catch(error => console.error('Erro:', error));
    }
} 