document.addEventListener('DOMContentLoaded', function() {
    // Função para animar contagem
    function animateCount(element, target) {
        // Verifica se target é um número válido
        if (isNaN(target) || target === null) {
            element.textContent = '0';
            return;
        }

        let current = 0;
        const duration = 1000; // 1 segundo
        const steps = 50;
        const increment = target / steps;
        const interval = duration / steps;

        const counter = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = Math.round(target);
                clearInterval(counter);
            } else {
                element.textContent = Math.floor(current);
            }
        }, interval);
    }

    // Função para verificar se o elemento está visível na viewport
    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Iniciar animação quando os cards estiverem visíveis
    function startCountAnimation() {
        const numbers = document.querySelectorAll('.number');
        numbers.forEach(number => {
            if (isElementInViewport(number) && !number.classList.contains('animated')) {
                // Pega o texto atual se não houver data-count
                const currentValue = number.textContent.trim();
                // Remove qualquer caractere não numérico
                const numericValue = currentValue.replace(/[^\d]/g, '');
                const target = parseInt(number.getAttribute('data-count') || numericValue || '0');
                
                if (!isNaN(target)) {
                    animateCount(number, target);
                    number.classList.add('animated');
                }
            }
        });
    }

    // Iniciar animação no carregamento e no scroll
    startCountAnimation();
    window.addEventListener('scroll', startCountAnimation);

    // Atualizar contagens em tempo real
    function updateCounts() {
        fetch('get_counts.php')
            .then(response => response.json())
            .then(data => {
                // Atualiza os números apenas se os valores forem válidos
                if (data.produtos !== undefined && !isNaN(data.produtos)) {
                    const produtosElement = document.querySelector('#produtos-card .number');
                    produtosElement.setAttribute('data-count', data.produtos);
                    produtosElement.textContent = data.produtos;
                }
                if (data.clientes !== undefined && !isNaN(data.clientes)) {
                    const clientesElement = document.querySelector('#clientes-card .number');
                    clientesElement.setAttribute('data-count', data.clientes);
                    clientesElement.textContent = data.clientes;
                }
                if (data.vendas !== undefined && !isNaN(data.vendas)) {
                    const vendasElement = document.querySelector('#vendas-card .number');
                    vendasElement.setAttribute('data-count', data.vendas);
                    vendasElement.textContent = data.vendas;
                }
                
                // Atualiza os status das vendas
                if (data.vendas_pendentes !== undefined && !isNaN(data.vendas_pendentes)) {
                    document.querySelector('.status-card.pendente .number').textContent = data.vendas_pendentes;
                }
                if (data.vendas_confirmadas !== undefined && !isNaN(data.vendas_confirmadas)) {
                    document.querySelector('.status-card.confirmado .number').textContent = data.vendas_confirmadas;
                }
                if (data.vendas_entregues !== undefined && !isNaN(data.vendas_entregues)) {
                    document.querySelector('.status-card.entregue .number').textContent = data.vendas_entregues;
                }

                startCountAnimation();
            })
            .catch(error => {
                console.error('Erro ao atualizar contagens:', error);
            });
    }

    // Atualizar contagens a cada 30 segundos
    setInterval(updateCounts, 30000);
}); 