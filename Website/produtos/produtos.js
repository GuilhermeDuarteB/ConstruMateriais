// Função para adicionar ao carrinho
function adicionarAoCarrinho(produtoId) {
    fetch('../carrinho/adicionar_ao_carrinho.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `produtoId=${produtoId}&quantidade=1`
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

// Atualização automática ao mudar filtros
document.querySelectorAll('.filtros-form input[type="radio"], .filtros-form select').forEach(element => {
    element.addEventListener('change', () => {
        document.querySelector('.filtros-form').submit();
    });
});

// Debounce para busca por texto
let timeout = null;
document.querySelector('input[name="busca"]').addEventListener('input', (e) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        document.querySelector('.filtros-form').submit();
    }, 500);
}); 