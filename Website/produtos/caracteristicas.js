document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidade do acordeão de características
    const accordionBtns = document.querySelectorAll('.accordion-btn');
    
    if (accordionBtns.length > 0) { // Verifica se existe algum botão antes de adicionar os eventos
        accordionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Encontra o conteúdo associado a este botão específico
                const accordionContent = this.nextElementSibling;
                
                if (accordionContent) { // Verifica se o conteúdo existe
                    // Toggle classes ativas
                    this.classList.toggle('active');
                    accordionContent.classList.toggle('active');
                    
                    // Rotacionar ícone
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.style.transform = this.classList.contains('active') 
                            ? 'rotate(180deg)' 
                            : 'rotate(0)';
                    }
                    
                    // Animar altura
                    if (accordionContent.classList.contains('active')) {
                        accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
                    } else {
                        accordionContent.style.maxHeight = "0";
                    }
                }
            });
        });
    }
}); 