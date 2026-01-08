// FILTROS SIMPLIFICADOS E FUNCIONAIS
document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const btnTodos = document.getElementById('btn-todos');
    const filtroCategoria = document.getElementById('filtro-categoria');
    const filtroEstante = document.getElementById('filtro-estante');
    const inputBusca = document.getElementById('input-busca');
    const formBusca = document.getElementById('form-busca');
    
    // Estado simples
    let filtroAtual = {
        categoria: '',
        estante: '',
        busca: ''
    };
    
    // ========== FUNÇÃO PRINCIPAL ==========
    function aplicarFiltros() {
        const cards = document.querySelectorAll('.item-card-6');
        let visiveis = 0;
        
        cards.forEach(card => {
            const tipo = card.dataset.tipo;
            const categoria = card.dataset.categoria || '';
            const estante = card.dataset.estante || '';
            const titulo = card.querySelector('.item-titulo-6').textContent.toLowerCase();
            
            let mostrar = true;
            
            // FILTRO 1: CATEGORIA (APENAS para produtos)
            if (filtroAtual.categoria) {
                if (tipo !== 'produto') {
                    mostrar = false; // Não é produto, não mostra
                } else if (categoria !== filtroAtual.categoria) {
                    mostrar = false; // É produto mas categoria diferente
                }
            }
            
            // FILTRO 2: ESTANTE (APENAS para livros)
            if (filtroAtual.estante) {
                if (tipo !== 'livro') {
                    mostrar = false; // Não é livro, não mostra
                } else if (estante !== filtroAtual.estante) {
                    mostrar = false; // É livro mas estante diferente
                }
            }
            
            // FILTRO 3: BUSCA (para todos)
            if (filtroAtual.busca) {
                const buscaTermo = filtroAtual.busca.toLowerCase();
                if (!titulo.includes(buscaTermo)) {
                    mostrar = false;
                }
            }
            
            // Aplicar
            card.style.display = mostrar ? 'flex' : 'none';
            if (mostrar) visiveis++;
        });
        
        // Atualizar contador
        atualizarContador(visiveis);
    }
    
    function atualizarContador(total) {
        const contador = document.getElementById('total-itens');
        if (contador) {
            contador.textContent = total + ' itens';
        }
    }
    
    // ========== EVENTOS SIMPLES ==========
    
    // BOTÃO "TODOS" - Limpa tudo
    if (btnTodos) {
        btnTodos.addEventListener('click', function() {
            // Limpar valores
            if (filtroCategoria) filtroCategoria.value = '';
            if (filtroEstante) filtroEstante.value = '';
            if (inputBusca) inputBusca.value = '';
            
            // Limpar estado
            filtroAtual = {
                categoria: '',
                estante: '',
                busca: ''
            };
            
            // Aplicar
            aplicarFiltros();
        });
    }
    
    // SELECT CATEGORIA - Só mostra produtos da categoria
    if (filtroCategoria) {
        filtroCategoria.addEventListener('change', function() {
            // Se selecionou categoria, limpa estante
            if (this.value && filtroEstante) {
                filtroEstante.value = '';
                filtroAtual.estante = '';
            }
            
            filtroAtual.categoria = this.value;
            aplicarFiltros();
        });
    }
    
    // SELECT ESTANTE - Só mostra livros da estante
    if (filtroEstante) {
        filtroEstante.addEventListener('change', function() {
            // Se selecionou estante, limpa categoria
            if (this.value && filtroCategoria) {
                filtroCategoria.value = '';
                filtroAtual.categoria = '';
            }
            
            filtroAtual.estante = this.value;
            aplicarFiltros();
        });
    }
    
    // BUSCA
    if (formBusca) {
        formBusca.addEventListener('submit', function(e) {
            e.preventDefault();
            filtroAtual.busca = inputBusca.value.trim();
            aplicarFiltros();
        });
        
        // Busca em tempo real
        inputBusca.addEventListener('input', function() {
            filtroAtual.busca = this.value.trim();
            aplicarFiltros();
        });
    }
    
    // ========== INICIALIZAÇÃO ==========
    function inicializar() {
        // Verificar URL para filtros salvos
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('categoria') && filtroCategoria) {
            filtroCategoria.value = urlParams.get('categoria');
            filtroAtual.categoria = filtroCategoria.value;
        }
        
        if (urlParams.has('estante') && filtroEstante) {
            filtroEstante.value = urlParams.get('estante');
            filtroAtual.estante = filtroEstante.value;
        }
        
        if (urlParams.has('busca') && inputBusca) {
            inputBusca.value = urlParams.get('busca');
            filtroAtual.busca = inputBusca.value;
        }
        
        // Aplicar filtros iniciais
        setTimeout(aplicarFiltros, 100);
    }
    
    inicializar();
});