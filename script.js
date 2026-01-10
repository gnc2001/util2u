// FILTROS MELHORADOS - COM "TODAS COISAS" E "TODOS LIVROS"
document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const btnTodos = document.getElementById('btn-todos');
    const filtroCategoria = document.getElementById('filtro-categoria');
    const filtroEstante = document.getElementById('filtro-estante');
    const inputBusca = document.getElementById('input-busca');
    const formBusca = document.getElementById('form-busca');
    
    // Estado
    let filtroAtual = {
        tipo: '',           // 'produto', 'livro', ou '' para todos
        categoria: '',      // ID da categoria ou 'todas_coisas'
        estante: '',        // Nome da estante ou 'todos_livros'
        busca: ''
    };
    
    // ========== APLICAR FILTROS ==========
    function aplicarFiltros() {
        const cards = document.querySelectorAll('.item-card-6');
        let visiveis = 0;
        
        cards.forEach(card => {
            const tipo = card.dataset.tipo;
            const categoria = card.dataset.categoria || '';
            const estante = card.dataset.estante || '';
            const titulo = card.querySelector('.item-titulo-6').textContent.toLowerCase();
            
            let mostrar = true;
            
            // FILTRO 1: TIPO GERAL (TODAS COISAS ou TODOS LIVROS)
            if (filtroAtual.tipo === 'produto' && tipo !== 'produto') {
                mostrar = false;
            }
            if (filtroAtual.tipo === 'livro' && tipo !== 'livro') {
                mostrar = false;
            }
            
            // FILTRO 2: CATEGORIA ESPECÍFICA (só se for produto)
            if (filtroAtual.categoria && filtroAtual.categoria !== 'todas_coisas') {
                if (tipo !== 'produto' || categoria !== filtroAtual.categoria) {
                    mostrar = false;
                }
            }
            
            // FILTRO 3: ESTANTE ESPECÍFICA (só se for livro)
            if (filtroAtual.estante && filtroAtual.estante !== 'todos_livros') {
                if (tipo !== 'livro' || estante !== filtroAtual.estante) {
                    mostrar = false;
                }
            }
            
            // FILTRO 4: BUSCA
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
        
        atualizarContador(visiveis);
        atualizarURL();
    }
    
    function atualizarContador(total) {
        const contador = document.getElementById('total-itens');
        if (contador) {
            contador.textContent = total + ' itens';
        }
    }
    
    function atualizarURL() {
        const params = new URLSearchParams();
        
        if (filtroAtual.busca) params.set('busca', filtroAtual.busca);
        if (filtroAtual.categoria) params.set('categoria', filtroAtual.categoria);
        if (filtroAtual.estante) params.set('estante', filtroAtual.estante);
        
        const novaURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.replaceState({}, '', novaURL);
    }
    
    function limparFiltros() {
        // Resetar selects
        if (filtroCategoria) filtroCategoria.value = '';
        if (filtroEstante) filtroEstante.value = '';
        if (inputBusca) inputBusca.value = '';
        
        // Resetar estado
        filtroAtual = {
            tipo: '',
            categoria: '',
            estante: '',
            busca: ''
        };
    }
    
    // ========== EVENTOS ==========
    
    // BOTÃO "TODOS"
    if (btnTodos) {
        btnTodos.addEventListener('click', function() {
            limparFiltros();
            aplicarFiltros();
        });
    }
    
    // SELECT CATEGORIA
    if (filtroCategoria) {
        filtroCategoria.addEventListener('change', function() {
            // Se selecionou algo em categoria, limpa estante
            if (this.value && filtroEstante) {
                filtroEstante.value = '';
                filtroAtual.estante = '';
                filtroAtual.tipo = '';
            }
            
            // Atualizar estado
            if (this.value === 'todas_coisas') {
                filtroAtual.tipo = 'produto';
                filtroAtual.categoria = 'todas_coisas';
            } else if (this.value) {
                filtroAtual.tipo = 'produto';
                filtroAtual.categoria = this.value;
            } else {
                filtroAtual.tipo = '';
                filtroAtual.categoria = '';
            }
            
            aplicarFiltros();
        });
    }
    
    // SELECT ESTANTE
    if (filtroEstante) {
        filtroEstante.addEventListener('change', function() {
            // Se selecionou algo em estante, limpa categoria
            if (this.value && filtroCategoria) {
                filtroCategoria.value = '';
                filtroAtual.categoria = '';
                filtroAtual.tipo = '';
            }
            
            // Atualizar estado
            if (this.value === 'todos_livros') {
                filtroAtual.tipo = 'livro';
                filtroAtual.estante = 'todos_livros';
            } else if (this.value) {
                filtroAtual.tipo = 'livro';
                filtroAtual.estante = this.value;
            } else {
                filtroAtual.tipo = '';
                filtroAtual.estante = '';
            }
            
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
        
        inputBusca.addEventListener('input', function() {
            filtroAtual.busca = this.value.trim();
            aplicarFiltros();
        });
    }
    
    // ========== INICIALIZAÇÃO ==========
    function inicializar() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Categoria
        if (urlParams.has('categoria') && filtroCategoria) {
            const cat = urlParams.get('categoria');
            filtroCategoria.value = cat;
            
            if (cat === 'todas_coisas') {
                filtroAtual.tipo = 'produto';
                filtroAtual.categoria = 'todas_coisas';
            } else {
                filtroAtual.tipo = 'produto';
                filtroAtual.categoria = cat;
            }
        }
        
        // Estante
        if (urlParams.has('estante') && filtroEstante) {
            const est = urlParams.get('estante');
            filtroEstante.value = est;
            
            if (est === 'todos_livros') {
                filtroAtual.tipo = 'livro';
                filtroAtual.estante = 'todos_livros';
            } else {
                filtroAtual.tipo = 'livro';
                filtroAtual.estante = est;
            }
        }
        
        // Busca
        if (urlParams.has('busca') && inputBusca) {
            inputBusca.value = urlParams.get('busca');
            filtroAtual.busca = inputBusca.value;
        }
        
        // Aplicar
        setTimeout(aplicarFiltros, 100);
    }
    
    inicializar();
});