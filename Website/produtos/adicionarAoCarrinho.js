function adicionarAoCarrinho(produtoId) {
    const quantidade = document.getElementById('quantidade').value;
    
    fetch('../carrinho/adicionar_ao_carrinho.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `produtoId=${produtoId}&quantidade=${quantidade}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Produto adicionado ao carrinho com sucesso!');
        } else {
            alert(data.message);
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Ocorreu um erro ao adicionar o produto ao carrinho.');
    });
}