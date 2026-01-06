// script.js - Versão atualizada sem preços
document.addEventListener('DOMContentLoaded', function() {
    console.log('Grande Desapego - Site carregado!');
    
    const cards = document.querySelectorAll('.item-card');
    const totalElement = document.getElementById('total-itens');
    const contadorElement = document.getElementById('contador-itens');
    const filtroCategoria = document.getElementById('categoria');
    const botoesStatus = document.querySelectorAll('.btn-status');
    
    // Atualizar contadores
    if(totalElement && contadorElement) {
        const total = cards.length;
        totalElement.textContent = total + ' itens disponíveis';
        contadorElement.textContent = total;
    }
    
    // Filtro por categoria e status
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
            
            if(mostrar) {
                card.style.display = 'flex';
                visiveis++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Atualizar contadores
        if(totalElement) {
            totalElement.textContent = visiveis + ' itens disponíveis';
        }
        if(contadorElement) {
            contadorElement.textContent = visiveis;
        }
        
        // Mostrar mensagem se nenhum item visível
        const grid = document.getElementById('grid-produtos');
        if (grid && visiveis === 0) {
            const mensagem = document.createElement('div');
            mensagem.className = 'sem-resultados';
            mensagem.innerHTML = `
                <i class="fas fa-search" style="font-size: 3rem; color: #95a5a6; margin-bottom: 1rem;"></i>
                <h3>Nenhum item encontrado</h3>
                <p>Tente ajustar os filtros para ver mais resultados.</p>
            `;
            
            // Verificar se já existe mensagem
            const msgExistente = grid.querySelector('.sem-resultados');
            if (!msgExistente) {
                grid.appendChild(mensagem);
            }
        } else {
            // Remover mensagem se existir
            const msgExistente = grid.querySelector('.sem-resultados');
            if (msgExistente) {
                msgExistente.remove();
            }
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
    
    // Efeitos visuais
    cards.forEach(card => {
        // Efeito hover
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '100';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
        
        // Clique nos logos - abrir em nova aba
        const logos = card.querySelectorAll('.mp-link');
        logos.forEach(logo => {
            logo.addEventListener('click', function(e) {
                e.stopPropagation(); // Não propagar para o card
                // Link já abre em nova aba via target="_blank"
            });
        });
    });
    
    // Inicializar filtros
    aplicarFiltros();
    
    // Efeito de carregamento
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
    
    // Transição suave ao entrar
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';
});

// Estilo para mensagem de sem resultados
const style = document.createElement('style');
style.textContent = `
    .sem-resultados {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin: 2rem auto;
        max-width: 500px;
    }
    
    .sem-resultados h3 {
        color: #7f8c8d;
        margin-bottom: 0.5rem;
    }
    
    .sem-resultados p {
        color: #95a5a6;
    }
`;
document.head.appendChild(style);