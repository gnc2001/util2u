// script.js - Versão simplificada para 6 colunas
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.item-card-6');
    const totalElement = document.getElementById('total-itens');
    const filtroCategoria = document.getElementById('categoria');
    const botoesStatus = document.querySelectorAll('.btn-status');
    
    // Atualizar contador
    if(totalElement) {
        totalElement.textContent = cards.length + ' itens disponíveis';
    }
    
    // Filtro simples
    function aplicarFiltros() {
        const categoria = filtroCategoria.value;
        const statusAtivo = document.querySelector('.btn-status.ativo').dataset.status;
        let visiveis = 0;
        
        cards.forEach(card => {
            const tipo = card.dataset.tipo;
            const status = card.dataset.status;
            let mostrar = true;
            
            // Filtro por categoria
            if (categoria !== 'todos') {
                if (categoria === 'livro' && tipo !== 'livro') mostrar = false;
                if (categoria === 'produto' && tipo !== 'produto') mostrar = false;
                if (categoria === 'doacao' && status !== 'doacao') mostrar = false;
            }
            
            // Filtro por status
            if (statusAtivo !== 'todos' && status !== statusAtivo) {
                mostrar = false;
            }
            
            card.style.display = mostrar ? 'flex' : 'none';
            if(mostrar) visiveis++;
        });
        
        // Atualizar contador
        if(totalElement) {
            totalElement.textContent = visiveis + ' itens disponíveis';
        }
    }
    
    // Event listeners
    if(filtroCategoria) {
        filtroCategoria.addEventListener('change', aplicarFiltros);
    }
    
    botoesStatus.forEach(botao => {
        botao.addEventListener('click', function() {
            botoesStatus.forEach(b => b.classList.remove('ativo'));
            this.classList.add('ativo');
            aplicarFiltros();
        });
    });
    
    // Efeito hover simples
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });
    });
});