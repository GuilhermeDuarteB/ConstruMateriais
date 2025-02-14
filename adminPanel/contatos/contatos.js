function verMensagem(contatoId) {
    fetch(`get_mensagem.php?id=${contatoId}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.querySelector('#mensagemModal .modal-body');
            modalBody.innerHTML = `
                <div class="mensagem-info">
                    <p><strong>De:</strong> ${data.nome}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Assunto:</strong> ${data.assunto}</p>
                    <p><strong>Data:</strong> ${data.data}</p>
                </div>
                <div class="mensagem-content">
                    <p class="mensagem-texto">${data.mensagem}</p>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('mensagemModal'));
            modal.show();
        })
        .catch(error => console.error('Erro:', error));
} 