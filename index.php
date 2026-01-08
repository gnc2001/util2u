<?php
require_once 'config.php';

// Verifica qual seção o usuário quer ver
$tipo = $_GET['tipo'] ?? ''; // Pode ser: 'livros', 'produtos', ou vazio (ambos)

// CONSULTA DE PRODUTOS
$query_produtos = "
    SELECT 
        'produto' as tipo,
        g.id,
        g.nome as titulo,
        COALESCE(NULLIF(g.imgprincipal, ''), g.icone, 'img/sem-foto.jpg') as imagem_principal,
        g.status,
        g.categoria as categoria_id,
        c.cat as categoria_nome,
        g.olx1, g.olx2, g.ml1, g.ml2, g.ev, g.enjoei, g.amazon, g.shopee
    FROM geral g
    LEFT JOIN categorias c ON g.categoria = c.id
    WHERE (g.excluido IS NULL OR g.excluido = 0)
    AND g.status IN ('disponivel', 'doacao')
    ORDER BY g.id DESC
";

// CONSULTA DE LIVROS
$query_livros = "
    SELECT 
        'livro' as tipo,
        l.ID as id,
        l.titulo,
        COALESCE(NULLIF(l.imgprincipal, ''), l.icone, 'img/sem-foto.jpg') as imagem_principal,
        CASE 
            WHEN l.vendido = 1 THEN 'vendido'
            WHEN l.perdido = 1 THEN 'vendido'
            ELSE 'disponivel'
        END as status,
        l.estante,
        l.olx1, l.olx2, l.ml1, l.ml2, l.ev, l.enjoei, l.amazon, l.shopee
    FROM livros l
    WHERE (l.vendido IS NULL OR l.vendido = 0) 
    AND (l.perdido IS NULL OR l.perdido = 0)
    ORDER BY l.ID DESC
";

// Executa consultas conforme o tipo selecionado
$itens = [];

if ($tipo === 'livros') {
    // Apenas livros
    $itens = $pdo->query($query_livros)->fetchAll();
    $titulo_pagina = "TODOS OS LIVROS";
    
} elseif ($tipo === 'produtos') {
    // Apenas produtos
    $itens = $pdo->query($query_produtos)->fetchAll();
    $titulo_pagina = "TODAS AS COISAS";
    
} else {
    // Página inicial: ambos (limitados)
    $produtos = $pdo->query($query_produtos . " LIMIT 30")->fetchAll();
    $livros = $pdo->query($query_livros . " LIMIT 30")->fetchAll();
    $itens = array_merge($produtos, $livros);
    usort($itens, function($a, $b) { return $b['id'] - $a['id']; });
    $itens = array_slice($itens, 0, 60);
    $titulo_pagina = "Página Inicial";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usados & Úteis | Vendas & Doações</title>
    <link rel="stylesheet" href="style.css?v=4.45433">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- FAIXA 1 SIMPLIFICADA -->
    <header class="faixa-1">
      
            <img src="img/logo2U-120.png" alt="Usados & Úteis" class="logo-transparente">

    </header>

<!-- FAIXA 2 - FILTROS INTELIGENTES -->
<section class="faixa-2">
    <div class="filtros-container">
        
        <!-- BUSCA -->
        <div class="grupo-busca">
            <form method="GET" action="" class="form-busca" id="form-busca">
                <input type="text" 
                       name="busca" 
                       placeholder="🔍 Pesquisar itens..." 
                       value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>"
                       class="campo-busca"
                       id="input-busca">
                <button type="submit" class="btn-busca-lupa" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <div class="separador">|</div>
        
        <!-- CATEGORIAS E ESTANTES -->
        <div class="grupo-categorias">
            <!-- Categorias -->
            <div class="filtro-select">
                <select name="categoria" id="filtro-categoria" class="select-estilizado" title="Filtrar por categoria">
                    <option value="">📦 TODAS COISAS</option>
                    <?php
                    $categorias = $pdo->query("SELECT id, cat FROM categorias ORDER BY cat")->fetchAll();
                    foreach ($categorias as $cat):
                        $selected = ($_GET['categoria'] ?? '') == $cat['id'] ? 'selected' : '';
                    ?>
                    <option value="<?= $cat['id'] ?>" <?= $selected ?>>
                        📦 <?= htmlspecialchars($cat['cat']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <span class="texto-ou">ou</span>
            
            <!-- Estantes -->
            <div class="filtro-select">
                <select name="estante" id="filtro-estante" class="select-estilizado" title="Filtrar por estante">
                    <option value="">📚 TODOS LIVROS</option>
                    <?php
                    $estantes = $pdo->query("SELECT estante FROM estante ORDER BY estante")->fetchAll();
                    foreach ($estantes as $est):
                        $selected = ($_GET['estante'] ?? '') == $est['estante'] ? 'selected' : '';
                    ?>
                    <option value="<?= htmlspecialchars($est['estante']) ?>" <?= $selected ?>>
                        📚 <?= htmlspecialchars($est['estante']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="separador">|</div>
        
        <!-- BOTÕES -->
        <div class="grupo-botoes">
            <button type="button" class="btn-filtro" id="btn-todos" title="Mostrar todos os itens">
                <i class="fas fa-globe"></i> Todos
            </button>
        </div>
        
        <!-- MENU HAMBÚRGUER -->
        <button class="menu-hamburguer" id="menu-hamburguer" aria-label="Abrir menu">
            <i class="fas fa-bars"></i>
        </button>
        
    </div>
    
    <!-- MENU MOBILE -->
    <div class="menu-mobile" id="menu-mobile">
        <div class="menu-conteudo">
            <!-- Conteúdo igual, mas responsivo -->
        </div>
    </div>
</section>
    <!-- FAIXA 3 - Grid 6 por linha -->
    <main class="faixa-3">
        <?php if(empty($itens)): ?>
            <div class="sem-itens">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <h2>Nenhum item disponível no momento</h2>
            </div>
        <?php else: ?>
            <div class="grid-container-6" id="grid-produtos">
                <?php foreach ($itens as $item): ?>
                    <?php 
                    $isDoacao = ($item['status'] == 'doacao');
                    $marketplaces = [];
                    $mps = ['olx1', 'olx2', 'ml1', 'ml2', 'ev', 'enjoei', 'amazon', 'shopee'];
                    
                    foreach ($mps as $mp) {
                        if (!empty($item[$mp])) $marketplaces[] = $mp;
                    }
                    ?>
                    
<div class="item-card-6" 
     data-tipo="<?= $item['tipo'] ?>" 
     data-categoria="<?= $item['categoria_id'] ?? '' ?>"
     data-estante="<?= htmlspecialchars($item['estante'] ?? '') ?>"
     data-id="<?= $item['id'] ?>">
                        
                        <!-- Badge apenas para DOAÇÃO (vermelho) -->
                        <?php if($isDoacao): ?>
                            <div class="badge-doacao-simples">
                                DOAÇÃO
                            </div>
                        <?php endif; ?>
                        
                        <!-- Imagem -->
                        <a href="produto.php?tipo=<?= $item['tipo'] ?>&id=<?= $item['id'] ?>" class="imagem-link-6">
                            <div class="imagem-container-6">
                                <img src="<?= htmlspecialchars($item['imagem_principal']) ?>" 
                                     alt="<?= htmlspecialchars($item['titulo']) ?>"
                                     class="imagem-principal-6"
                                     onerror="this.onerror=null; this.src='img/sem-foto.jpg';">
                            </div>
                        </a>
                        
                        <!-- Título -->
                        <h3 class="item-titulo-6" title="<?= htmlspecialchars($item['titulo']) ?>">
                            <?= htmlspecialchars(mb_substr($item['titulo'], 0, 50)) ?>
                            <?= (mb_strlen($item['titulo']) > 50) ? '...' : '' ?>
                        </h3>
                        
                        <!-- Logos dos marketplaces -->
                        <div class="marketplace-logos-6">
                            <?php if(!empty($marketplaces)): 
                                $logoMap = [
                                    'olx1' => 'olx.png', 'olx2' => 'olx.png',
                                    'ml1' => 'mercadolivre.png', 'ml2' => 'mercadolivre.png',
                                    'ev' => 'estantevirtual.png', 'enjoei' => 'enjoei.png',
                                    'amazon' => 'amazon.png', 'shopee' => 'shopee.png'
                                ];
                                
                                // Mostrar até 3 logos
                                $marketplaces = array_slice($marketplaces, 0, 3);
                                foreach ($marketplaces as $mp):
                                    if(isset($logoMap[$mp])):
                            ?>
                                <a href="<?= htmlspecialchars($item[$mp]) ?>" 
                                   target="_blank"
                                   class="mp-link-6"
                                   title="Ver no <?= strtoupper($mp) ?>">
                                    <img src="img/<?= $logoMap[$mp] ?>" 
                                         alt="<?= strtoupper($mp) ?>" 
                                         class="logo-mp-6">
                                </a>
                            <?php 
                                    endif;
                                endforeach; 
                            else: ?>
                                <span class="sem-links-6">Sem links</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="script.js"></script>
</body>
</html>