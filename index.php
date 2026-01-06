<?php
require_once 'config.php';

// Consulta combinada de produtos e livros - COM imgprincipal
$query = "
    (SELECT 
        'produto' as tipo,
        id,
        nome as titulo,
        COALESCE(NULLIF(imgprincipal, ''), icone) as imagem_principal,  -- PRIMEIRA PRIORIDADE: imgprincipal
        status,
        olx1, olx2, ml1, ml2, ev, enjoei, amazon, shopee
     FROM geral 
     WHERE (excluido IS NULL OR excluido = 0)
     AND status IN ('disponivel', 'doacao')
     ORDER BY id DESC)
    
    UNION ALL
    
    (SELECT 
        'livro' as tipo,
        ID as id,
        titulo,
        COALESCE(NULLIF(imgprincipal, ''), icone) as imagem_principal,  -- PRIMEIRA PRIORIDADE: imgprincipal
        CASE 
            WHEN vendido = 1 THEN 'vendido'
            WHEN perdido = 1 THEN 'vendido'
            ELSE 'disponivel'
        END as status,
        olx1, olx2, ml1, ml2, ev, enjoei, amazon, shopee
     FROM livros 
     WHERE (vendido IS NULL OR vendido = 0) 
     AND (perdido IS NULL OR perdido = 0)
     ORDER BY ID DESC)
    
    ORDER BY id DESC
    LIMIT 40
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$itens = $stmt->fetchAll();

// Para debug - remover depois
// echo "<pre>"; print_r($itens); echo "</pre>";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2U - Usados & Úteis</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- PRIMEIRA FAIXA: Cabeçalho -->
    <header class="faixa-1">
        <div class="logo-container">
            <img src="img/logo2Upq.png" alt="Logo">
      </div>
    </header>

    <!-- SEGUNDA FAIXA: Filtros -->
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

    <!-- TERCEIRA FAIXA: Grid de produtos -->
    <main class="faixa-3">
        <?php if(empty($itens)): ?>
            <div class="sem-itens">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <h2>Nenhum item disponível no momento</h2>
                <p>Em breve teremos novos itens para venda e doação.</p>
            </div>
        <?php else: ?>
            <div class="grid-container" id="grid-produtos">
                <?php foreach ($itens as $item): ?>
                    <?php 
                    $isDoacao = ($item['status'] == 'doacao');
                    $marketplaces = [];
                    $mps = ['olx1', 'olx2', 'ml1', 'ml2', 'ev', 'enjoei', 'amazon', 'shopee'];
                    
                    foreach ($mps as $mp) {
                        if (!empty($item[$mp])) $marketplaces[] = $mp;
                    }
                    ?>
                    
                    <div class="item-card" 
                         data-tipo="<?= $item['tipo'] ?>" 
                         data-status="<?= $item['status'] ?>"
                         data-id="<?= $item['id'] ?>">
                        
                        <!-- Badge de Doação (AGORA MAIOR E MAIS VISÍVEL) -->
                        <?php if($isDoacao): ?>
                            <div class="badge-doacao">
                                <i class="fas fa-gift"></i> DOAÇÃO
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge Livro -->
                        <?php if($item['tipo'] == 'livro'): ?>
                            <div class="badge-livro">
                                <i class="fas fa-book"></i> LIVRO
                            </div>
                        <?php endif; ?>
                        
                        <!-- Imagem principal (CLICÁVEL para página de detalhes) -->
                        <a href="produto.php?tipo=<?= $item['tipo'] ?>&id=<?= $item['id'] ?>" class="imagem-link">
                            <div class="imagem-container">
                                <?php if(!empty($item['imagem_principal'])): ?>
                                    <img src="<?= htmlspecialchars($item['imagem_principal']) ?>" 
                                         alt="<?= htmlspecialchars($item['titulo']) ?>"
                                         class="imagem-principal"
                                         onerror="this.onerror=null; this.src='img/sem-foto.jpg';">
                                <?php else: ?>
                                    <img src="img/sem-foto.jpg" alt="Sem foto" class="imagem-principal">
                                <?php endif; ?>
                                
                                <!-- Overlay para zoom -->
                                <div class="imagem-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Título (agora com mais espaço) -->
                        <h3 class="item-titulo" title="<?= htmlspecialchars($item['titulo']) ?>">
                            <?= htmlspecialchars(mb_substr($item['titulo'], 0, 60)) ?>
                            <?= (mb_strlen($item['titulo']) > 60) ? '...' : '' ?>
                        </h3>
                        
                        <!-- REMOVIDO: Campo de preço -->
                        
                        <!-- Logos dos marketplaces disponíveis -->
                        <div class="marketplace-logos">
                            <?php if(!empty($marketplaces)): 
                                $logoMap = [
                                    'olx1' => 'olx.png', 'olx2' => 'olx.png',
                                    'ml1' => 'mercadolivre.png', 'ml2' => 'mercadolivre.png',
                                    'ev' => 'estantevirtual.png', 'enjoei' => 'enjoei.png',
                                    'amazon' => 'amazon.png', 'shopee' => 'shopee.png'
                                ];
                                
                                // Mostrar até 5 logos
                                $marketplaces = array_slice($marketplaces, 0, 5);
                                foreach ($marketplaces as $mp):
                                    if(isset($logoMap[$mp])):
                            ?>
                                <a href="<?= htmlspecialchars($item[$mp]) ?>" 
                                   target="_blank"
                                   class="mp-link"
                                   title="Ver no <?= strtoupper($mp) ?>">
                                    <img src="img/<?= $logoMap[$mp] ?>" 
                                         alt="<?= strtoupper($mp) ?>" 
                                         class="logo-mp">
                                </a>
                            <?php 
                                    endif;
                                endforeach; 
                            else: ?>
                                <span class="sem-links">Sem links ativos</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Botão para página de detalhes -->
                        <a href="produto.php?tipo=<?= $item['tipo'] ?>&id=<?= $item['id'] ?>" class="btn-detalhes">
                            <i class="fas fa-eye"></i> Ver detalhes
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer-content">
            <p><i class="fas fa-heart" style="color: #e74c3c;"></i> Obrigado por nos ajudar nessa jornada rumo a Portugal!</p>
            <p class="contato">Dúvidas? <i class="fab fa-whatsapp"></i> WhatsApp: (11) 98765-4321</p>
            <p class="meta">Meta: <span id="contador-itens"><?= count($itens) ?></span> itens para encontrar novos lares!</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>