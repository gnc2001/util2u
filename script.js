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


// FILTROS AVANÇADOS
document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const btnLivros = document.getElementById('btn-livros');
    const btnCoisas = document.getElementById('btn-coisas');
    const btnLimpar = document.getElementById('btn-limpar');
    const filtroCategoria = document.getElementById('filtro-categoria');
    const filtroEstante = document.getElementById('filtro-estante');
    const formBusca = document.querySelector('.form-busca');
    const menuHamburguer = document.getElementById('menu-hamburguer');
    const menuMobile = document.getElementById('menu-mobile');
    
    // Estado atual dos filtros
    let filtroAtivo = {
        tipo: 'todos',
        categoria: '',
        estante: '',
        busca: ''
    };
    
    // Aplicar filtros
    function aplicarFiltros() {
        const cards = document.querySelectorAll('.item-card-6');
        let visiveis = 0;
        
        cards.forEach(card => {
            const tipo = card.dataset.tipo;
            const categoria = card.dataset.categoria || '';
            const estante = card.dataset.estante || '';
            const titulo = card.querySelector('.item-titulo-6').textContent.toLowerCase();
            
            let mostrar = true;
            
            // Filtro por tipo
            if (filtroAtivo.tipo !== 'todos' && tipo !== filtroAtivo.tipo) {
                mostrar = false;
            }
            
            // Filtro por categoria
            if (filtroAtivo.categoria && categoria !== filtroAtivo.categoria) {
                mostrar = false;
            }
            
            // Filtro por estante
            if (filtroAtivo.estante && estante !== filtroAtivo.estante) {
                mostrar = false;
            }
            
            // Filtro por busca
            if (filtroAtivo.busca) {
                const busca = filtroAtivo.busca.toLowerCase();
                if (!titulo.includes(busca)) {
                    mostrar = false;
                }
            }
            
            card.style.display = mostrar ? 'flex' : 'none';
            if (mostrar) visiveis++;
        });
        
        // Atualizar contador (se existir)
        const contador = document.getElementById('total-itens');
        if (contador) {
            contador.textContent = visiveis + ' itens';
        }
    }
    
    // Event Listeners
    if (btnLivros) {
        btnLivros.addEventListener('click', function() {
            btnLivros.classList.toggle('ativo');
            btnCoisas.classList.remove('ativo');
            filtroAtivo.tipo = btnLivros.classList.contains('ativo') ? 'livro' : 'todos';
            aplicarFiltros();
        });
    }
    
    if (btnCoisas) {
        btnCoisas.addEventListener('click', function() {
            btnCoisas.classList.toggle('ativo');
            btnLivros.classList.remove('ativo');
            filtroAtivo.tipo = btnCoisas.classList.contains('ativo') ? 'produto' : 'todos';
            aplicarFiltros();
        });
    }
    
    if (btnLimpar) {
        btnLimpar.addEventListener('click', function() {
            // Limpar todos os filtros
            btnLivros.classList.remove('ativo');
            btnCoisas.classList.remove('ativo');
            if (filtroCategoria) filtroCategoria.value = '';
            if (filtroEstante) filtroEstante.value = '';
            if (formBusca) formBusca.reset();
            
            filtroAtivo = {
                tipo: 'todos',
                categoria: '',
                estante: '',
                busca: ''
            };
            
            aplicarFiltros();
        });
    }
    
    if (filtroCategoria) {
        filtroCategoria.addEventListener('change', function() {
            filtroAtivo.categoria = this.value;
            aplicarFiltros();
        });
    }
    
    if (filtroEstante) {
        filtroEstante.addEventListener('change', function() {
            filtroAtivo.estante = this.value;
            aplicarFiltros();
        });
    }
    
    if (formBusca) {
        formBusca.addEventListener('submit', function(e) {
            e.preventDefault();
            const inputBusca = this.querySelector('input[name="busca"]');
            filtroAtivo.busca = inputBusca.value;
            aplicarFiltros();
        });
    }
    
    // Menu Hamburguer (Mobile)
    if (menuHamburguer && menuMobile) {
        menuHamburguer.addEventListener('click', function() {
            menuMobile.classList.toggle('ativo');
            this.innerHTML = menuMobile.classList.contains('ativo') 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        });
        
        // Fechar menu ao clicar fora
        document.addEventListener('click', function(e) {
            if (!menuMobile.contains(e.target) && !menuHamburguer.contains(e.target)) {
                menuMobile.classList.remove('ativo');
                menuHamburguer.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
    }
    
    // Carregar filtros da URL (se existirem)
    function carregarFiltrosDaURL() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('busca')) {
            filtroAtivo.busca = urlParams.get('busca');
            const inputBusca = document.querySelector('input[name="busca"]');
            if (inputBusca) inputBusca.value = filtroAtivo.busca;
        }
        
        if (urlParams.has('categoria')) {
            filtroAtivo.categoria = urlParams.get('categoria');
            if (filtroCategoria) filtroCategoria.value = filtroAtivo.categoria;
        }
        
        if (urlParams.has('estante')) {
            filtroAtivo.estante = urlParams.get('estante');
            if (filtroEstante) filtroEstante.value = filtroAtivo.estante;
        }
        
        // Aplicar filtros carregados
        setTimeout(aplicarFiltros, 100);
    }
    
    carregarFiltrosDaURL();
});

// FILTROS INTELIGENTES - COM AUTO-LIMPEZA
document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const btnLivros = document.getElementById('btn-livros');
    const btnCoisas = document.getElementById('btn-coisas');
    const btnTodos = document.getElementById('btn-todos');
    const filtroCategoria = document.getElementById('filtro-categoria');
    const filtroEstante = document.getElementById('filtro-estante');
    const inputBusca = document.getElementById('input-busca');
    const formBusca = document.getElementById('form-busca');
    const menuHamburguer = document.getElementById('menu-hamburguer');
    const menuMobile = document.getElementById('menu-mobile');
    
    // Estado dos filtros
    let filtrosAtivos = {
        tipo: 'todos',      // 'livro', 'produto', 'todos'
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
            
            // 1. Filtro por TIPO
            if (filtrosAtivos.tipo !== 'todos') {
                if (filtrosAtivos.tipo === 'livro' && tipo !== 'livro') mostrar = false;
                if (filtrosAtivos.tipo === 'produto' && tipo !== 'produto') mostrar = false;
            }
            
            // 2. Filtro por CATEGORIA (só aplica se for produto)
            if (filtrosAtivos.categoria && tipo === 'produto') {
                if (categoria !== filtrosAtivos.categoria) mostrar = false;
            }
            
            // 3. Filtro por ESTANTE (só aplica se for livro)
            if (filtrosAtivos.estante && tipo === 'livro') {
                if (estante !== filtrosAtivos.estante) mostrar = false;
            }
            
            // 4. Filtro por BUSCA (aplica a todos)
            if (filtrosAtivos.busca) {
                const buscaTermo = filtrosAtivos.busca.toLowerCase();
                if (!titulo.includes(buscaTermo)) mostrar = false;
            }
            
            // Aplicar visibilidade
            card.style.display = mostrar ? 'flex' : 'none';
            if (mostrar) visiveis++;
        });
        
        // Atualizar contador
        atualizarContador(visiveis);
        
        // Atualizar URL (opcional)
        atualizarURL();
    }
    
    // ========== FUNÇÕES AUXILIARES ==========
    function atualizarContador(total) {
        const contador = document.getElementById('total-itens');
        if (contador) {
            contador.textContent = total + ' itens';
        }
    }
    
    function atualizarURL() {
        // Opcional: Atualizar URL sem recarregar página
        const params = new URLSearchParams();
        if (filtrosAtivos.busca) params.set('busca', filtrosAtivos.busca);
        if (filtrosAtivos.categoria) params.set('categoria', filtrosAtivos.categoria);
        if (filtrosAtivos.estante) params.set('estante', filtrosAtivos.estante);
        
        const novaURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.replaceState({}, '', novaURL);
    }
    
    function limparOutrosFiltros(excecao) {
        // Limpa filtros que não são a exceção
        if (excecao !== 'tipo') {
            btnLivros?.classList.remove('ativo');
            btnCoisas?.classList.remove('ativo');
            btnTodos?.classList.add('ativo');
            filtrosAtivos.tipo = 'todos';
        }
        
        if (excecao !== 'categoria') {
            if (filtroCategoria) filtroCategoria.value = '';
            filtrosAtivos.categoria = '';
        }
        
        if (excecao !== 'estante') {
            if (filtroEstante) filtroEstante.value = '';
            filtrosAtivos.estante = '';
        }
        
        if (excecao !== 'busca') {
            if (inputBusca) inputBusca.value = '';
            filtrosAtivos.busca = '';
        }
    }
    
    // ========== EVENT LISTENERS ==========
    
    // BOTÃO "SÓ LIVROS"
    if (btnLivros) {
        btnLivros.addEventListener('click', function() {
            limparOutrosFiltros('tipo');
            
            this.classList.toggle('ativo');
            btnCoisas?.classList.remove('ativo');
            btnTodos?.classList.remove('ativo');
            
            filtrosAtivos.tipo = this.classList.contains('ativo') ? 'livro' : 'todos';
            
            // Se ativou "Só Livros", limpa categoria (não aplicável)
            if (this.classList.contains('ativo')) {
                filtroCategoria.value = '';
                filtrosAtivos.categoria = '';
            }
            
            aplicarFiltros();
        });
    }
    
    // BOTÃO "SÓ COISAS"
    if (btnCoisas) {
        btnCoisas.addEventListener('click', function() {
            limparOutrosFiltros('tipo');
            
            this.classList.toggle('ativo');
            btnLivros?.classList.remove('ativo');
            btnTodos?.classList.remove('ativo');
            
            filtrosAtivos.tipo = this.classList.contains('ativo') ? 'produto' : 'todos';
            
            // Se ativou "Só Coisas", limpa estante (não aplicável)
            if (this.classList.contains('ativo')) {
                filtroEstante.value = '';
                filtrosAtivos.estante = '';
            }
            
            aplicarFiltros();
        });
    }
    
    // BOTÃO "TODOS"
    if (btnTodos) {
        btnTodos.addEventListener('click', function() {
            limparOutrosFiltros('tipo');
            
            this.classList.add('ativo');
            btnLivros?.classList.remove('ativo');
            btnCoisas?.classList.remove('ativo');
            
            filtrosAtivos.tipo = 'todos';
            aplicarFiltros();
        });
    }
    
    // SELECT CATEGORIA
    if (filtroCategoria) {
        filtroCategoria.addEventListener('change', function() {
            if (this.value) {
                limparOutrosFiltros('categoria');
                
                // Se selecionou categoria, automaticamente filtra só produtos
                btnCoisas?.classList.add('ativo');
                btnLivros?.classList.remove('ativo');
                btnTodos?.classList.remove('ativo');
                filtrosAtivos.tipo = 'produto';
            }
            
            filtrosAtivos.categoria = this.value;
            aplicarFiltros();
        });
    }
    
    // SELECT ESTANTE
    if (filtroEstante) {
        filtroEstante.addEventListener('change', function() {
            if (this.value) {
                limparOutrosFiltros('estante');
                
                // Se selecionou estante, automaticamente filtra só livros
                btnLivros?.classList.add('ativo');
                btnCoisas?.classList.remove('ativo');
                btnTodos?.classList.remove('ativo');
                filtrosAtivos.tipo = 'livro';
            }
            
            filtrosAtivos.estante = this.value;
            aplicarFiltros();
        });
    }
    
    // BUSCA
    if (formBusca) {
        formBusca.addEventListener('submit', function(e) {
            e.preventDefault();
            filtrosAtivos.busca = inputBusca.value.trim();
            
            // Busca não limpa outros filtros (pode combinar)
            aplicarFiltros();
        });
        
        // Busca em tempo real (opcional)
        inputBusca.addEventListener('input', function() {
            filtrosAtivos.busca = this.value.trim();
            aplicarFiltros();
        });
    }
    
    // MENU HAMBÚRGUER
    if (menuHamburguer && menuMobile) {
        menuHamburguer.addEventListener('click', function(e) {
            e.stopPropagation();
            menuMobile.classList.toggle('ativo');
            this.innerHTML = menuMobile.classList.contains('ativo') 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        });
        
        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (!menuMobile.contains(e.target) && !menuHamburguer.contains(e.target)) {
                menuMobile.classList.remove('ativo');
                menuHamburguer.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
    }
    
    // ========== INICIALIZAÇÃO ==========
    function carregarFiltrosIniciais() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Busca
        if (urlParams.has('busca')) {
            filtrosAtivos.busca = urlParams.get('busca');
            if (inputBusca) inputBusca.value = filtrosAtivos.busca;
        }
        
        // Categoria
        if (urlParams.has('categoria')) {
            filtrosAtivos.categoria = urlParams.get('categoria');
            if (filtroCategoria) {
                filtroCategoria.value = filtrosAtivos.categoria;
                btnCoisas?.classList.add('ativo');
                btnTodos?.classList.remove('ativo');
                filtrosAtivos.tipo = 'produto';
            }
        }
        
        // Estante
        if (urlParams.has('estante')) {
            filtrosAtivos.estante = urlParams.get('estante');
            if (filtroEstante) {
                filtroEstante.value = filtrosAtivos.estante;
                btnLivros?.classList.add('ativo');
                btnTodos?.classList.remove('ativo');
                filtrosAtivos.tipo = 'livro';
            }
        }
        
        // Tipo (se não tem categoria/estante, mostra todos)
        if (!filtrosAtivos.categoria && !filtrosAtivos.estante) {
            btnTodos?.classList.add('ativo');
        }
        
        // Aplicar filtros iniciais
        setTimeout(aplicarFiltros, 100);
    }
    
    carregarFiltrosIniciais();
});