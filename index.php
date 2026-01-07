<?php
require_once 'config.php';

// CONSULTA CORRIGIDA - MOSTRA TUDO, mesmo sem imagem
$query_produtos = "
    SELECT 
        'produto' as tipo,
        id,
        nome as titulo,
        COALESCE(NULLIF(imgprincipal, ''), icone, 'img/sem-foto.jpg') as imagem_principal,
        status,
        olx1, olx2, ml1, ml2, ev, enjoei, amazon, shopee
    FROM geral 
    WHERE (excluido IS NULL OR excluido = 0)
    AND status IN ('disponivel', 'doacao')
    ORDER BY id DESC
    LIMIT 30
";

$query_livros = "
    SELECT 
        'livro' as tipo,
        ID as id,
        titulo,
        COALESCE(NULLIF(imgprincipal, ''), icone, 'img/sem-foto.jpg') as imagem_principal,
        CASE 
            WHEN vendido = 1 THEN 'vendido'
            WHEN perdido = 1 THEN 'vendido'
            ELSE 'disponivel'
        END as status,
        olx1, olx2, ml1, ml2, ev, enjoei, amazon, shopee
    FROM livros 
    WHERE (vendido IS NULL OR vendido = 0) 
    AND (perdido IS NULL OR perdido = 0)
    ORDER BY ID DESC
    LIMIT 30
";

$produtos = $pdo->query($query_produtos)->fetchAll();
$livros = $pdo->query($query_livros)->fetchAll();
$itens = array_merge($produtos, $livros);
usort($itens, function($a, $b) { return $b['id'] - $a['id']; });
$itens = array_slice($itens, 0, 60); // Aumentei para 60 itens
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usados & Úteis | Vendas & Doações</title>
    <link rel="stylesheet" href="style.css?v=4.233">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- FAIXA 1 SIMPLIFICADA -->
    <header class="faixa-1">
      
            <img src="img/logo2U-120.png" alt="Usados & Úteis" class="logo-transparente">

    </header>

    <!-- FAIXA 2 - Filtros -->
    <section class="faixa-2">
        <div class="filtros-container">
            <div class="contador-total">
                <i class="fas fa-boxes"></i>
                <span id="total-itens"><?= count($itens) ?> itens disponíveis</span>
            </div>
            
            <div class="filtro-categoria">
                <label for="categoria"><i class="fas fa-filter"></i> Tipo:</label>
                <select id="categoria">
                    <option value="todos">Todos os itens</option>
                    <option value="livro">Apenas Livros</option>
                    <option value="produto">Apenas Produtos</option>
                    <option value="doacao">Apenas Doações</option>
                </select>
            </div>
            
            <div class="status-botoes">
                <button class="btn-status ativo" data-status="todos">Todos</button>
                <button class="btn-status" data-status="disponivel">À venda</button>
                <button class="btn-status" data-status="doacao">Doação</button>
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
                         data-status="<?= $item['status'] ?>"
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