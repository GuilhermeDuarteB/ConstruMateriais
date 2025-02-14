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
    const selectHTML = `
        <select id="novoStatus" class="form-control">
            <option value="Pendente">Pendente</option>
            <option value="Confirmado">Confirmado</option>
            <option value="Enviado">Enviado</option>
            <option value="Entregue">Entregue</option>
            <option value="Cancelado">Cancelado</option>
        </select>
    `;
    
    Swal.fire({
        title: 'Atualizar Status',
        html: selectHTML,
        showCancelButton: true,
        confirmButtonText: 'Atualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const novoStatus = document.getElementById('novoStatus').value;
            
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Status atualizado com sucesso!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: data.error || 'Erro ao atualizar status'
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao atualizar status'
                });
            });
        }
    });
} 