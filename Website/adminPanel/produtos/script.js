document.addEventListener('DOMContentLoaded', function() {
    // Função para mostrar preview da imagem
    function showImagePreview(input, previewId) {
        const preview = document.getElementById(previewId);
        
        input.addEventListener('change', function() {
            while(preview.firstChild) {
                preview.removeChild(preview.firstChild);
            }

            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Inicializar preview para cada input de foto
    showImagePreview(document.getElementById('foto1'), 'preview1');
    showImagePreview(document.getElementById('foto2'), 'preview2');
    showImagePreview(document.getElementById('foto3'), 'preview3');

    // Validação do formulário
    document.querySelector('.produto-form').addEventListener('submit', function(e) {
        const codigo = document.getElementById('codigo').value;
        const preco = document.getElementById('preco').value;
        const quantidade = document.getElementById('quantidade').value;
        const estoqueMinimo = document.getElementById('estoque_minimo').value;

        if (parseFloat(preco) <= 0) {
            e.preventDefault();
            alert('O preço deve ser maior que zero.');
        }

        if (parseInt(quantidade) < 0) {
            e.preventDefault();
            alert('A quantidade não pode ser negativa.');
        }

        if (parseInt(estoqueMinimo) < 0) {
            e.preventDefault();
            alert('O estoque mínimo não pode ser negativo.');
        }
    });
});