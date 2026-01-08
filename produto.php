<?php
// produto.php - Página de detalhes do item
require_once 'config.php'; // Este fica SOMENTE no servidor

// ========== SEGURANÇA ==========
$tipo = $_GET['tipo'] ?? '';
$id = (int)($_GET['id'] ?? 0);

// Validar entrada
if (!in_array($tipo, ['produto', 'livro']) || $id <= 0) {
    header('Location: index.php');
    exit;
}

// ========== BUSCAR ITEM PRINCIPAL ==========
if ($tipo == 'produto') {
    $query = "SELECT * FROM geral WHERE id = :id AND (excluido IS NULL OR excluido = 0)";
    $tabela_imagens = 'imagens';
    $campo_imagem = 'item_id';
} else {
    $query = "SELECT * FROM livros WHERE ID = :id AND (vendido IS NULL OR vendido = 0) AND (perdido IS NULL OR perdido = 0)";
    $tabela_imagens = 'imagem_livros';
    $campo_imagem = 'id_livro';
}

$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();

// Se não encontrou, voltar para lista
if (!$item) {
    header('Location: index.php');
    exit;
}

// ========== BUSCAR IMAGENS ==========
$query_imagens = "SELECT caminho FROM $tabela_imagens WHERE $campo_imagem = :id ORDER BY caminho";
$stmt_imagens = $pdo->prepare($query_imagens);
$stmt_imagens->execute([':id' => $id]);
$imagens = $stmt_imagens->fetchAll(PDO::FETCH_COLUMN);

// Adicionar imagem principal no início (se existir)
$imagem_principal = $tipo == 'produto' ? ($item['imgprincipal'] ?? $item['icone'] ?? '') : ($item['imgprincipal'] ?? $item['icone'] ?? '');
if ($imagem_principal && !in_array($imagem_principal, $imagens)) {
    array_unshift($imagens, $imagem_principal);
}

// Se não tiver nenhuma imagem, usar placeholder
if (empty($imagens)) {
    $imagens = ['img/sem-foto.jpg'];
}

// ========== PREPARAR LINKS MARKETPLACE ==========
$marketplaces = [
    'OLX 1' => ['logo' => 'olx.png', 'url' => $item['olx1'] ?? ''],
    'OLX 2' => ['logo' => 'olx.png', 'url' => $item['olx2'] ?? ''],
    'Mercado Livre 1' => ['logo' => 'mercadolivre.png', 'url' => $item['ml1'] ?? ''],
    'Mercado Livre 2' => ['logo' => 'mercadolivre.png', 'url' => $item['ml2'] ?? ''],
    'Estante Virtual' => ['logo' => 'estantevirtual.png', 'url' => $item['ev'] ?? ''],
    'Enjoei' => ['logo' => 'enjoei.png', 'url' => $item['enjoei'] ?? ''],
    'Amazon' => ['logo' => 'amazon.png', 'url' => $item['amazon'] ?? ''],
    'Shopee' => ['logo' => 'shopee.png', 'url' => $item['shopee'] ?? '']
];

// Filtrar apenas os que têm URL
$marketplaces_ativos = array_filter($marketplaces, function($mp) {
    return !empty($mp['url']);
});

// ========== HTML ==========
$titulo_pagina = $tipo == 'produto' ? htmlspecialchars($item['nome']) : htmlspecialchars($item['titulo']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?>Usados & Úteis</title>
    <link rel="stylesheet" href="style.css?v=5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ESTILOS ESPECÍFICOS PARA PÁGINA DE DETALHES */
        .produto-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 5%;
        }
        
        .voltar-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #3498db;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .voltar-btn:hover {
            background: #2980b9;
            transform: translateX(-5px);
        }
        
        .produto-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        /* CARROSSEL */
        .carrossel-container {
            position: relative;
        }
        
        .imagem-principal {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 1rem;
        }
        
        .miniaturas {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.5rem 0;
        }
        
        .miniatura {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            opacity: 0.6;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .miniatura:hover, .miniatura.ativa {
            opacity: 1;
            border-color: #3498db;
        }
        
        /* INFORMAÇÕES */
        .info-container h1 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: 2.2rem;
        }
        
        .badge-tipo {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: <?= $tipo == 'produto' ? '#3498db' : '#9b59b6' ?>;
            color: white;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .badge-doacao {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-left: 1rem;
        }
        
        .descricao-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            line-height: 1.8;
        }
        
        /* DETALHES ESPECÍFICOS */
        .detalhes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        
        .detalhe-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #eaeaea;
        }
        
        .detalhe-item strong {
            display: block;
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        
        /* MARKETPLACES */
        .marketplaces-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #eee;
        }
        
        .marketplaces-section h3 {
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        
        .marketplaces-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }
        
        .marketplace-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            text-decoration: none;
            color: #2c3e50;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .marketplace-link:hover {
            background: white;
            border-color: #3498db;
            transform: translateY(-5px);
        }
        
        .marketplace-link img {
            width: 50px;
            height: 50px;
            margin-bottom: 0.8rem;
        }
        
        /* RESPONSIVIDADE */
        @media (max-width: 900px) {
            .produto-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .imagem-principal {
                height: 300px;
            }
        }
        
        @media (max-width: 600px) {
            .detalhes-grid {
                grid-template-columns: 1fr;
            }
            
            .marketplaces-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- CABEÇALHO SIMPLES -->
    <header class="faixa-1" style="padding: 1rem 5%;">
        <div class="logo-simples">
            <a href="index.php">
                <img src="img/logo2U-120.png" alt="Grande Desapego" class="logo-transparente">
            </a>
        </div>
    </header>
    
    <!-- CONTEÚDO PRINCIPAL -->
    <main class="produto-container">
        <!-- BOTÃO VOLTAR -->
        <a href="index.php" class="voltar-btn">
            <i class="fas fa-arrow-left"></i> Voltar para a lista
        </a>
        
        <div class="produto-content">
            <!-- COLUNA 1: IMAGENS -->
            <div class="carrossel-container">
                <!-- Imagem principal -->
                <img id="imagem-principal" src="<?= htmlspecialchars($imagens[0]) ?>" 
                     alt="<?= $titulo_pagina ?>" class="imagem-principal"
                     onerror="this.onerror=null; this.src='img/sem-foto.jpg';">
                
                <!-- Miniaturas (se tiver mais de uma imagem) -->
                <?php if (count($imagens) > 1): ?>
                <div class="miniaturas">
                    <?php foreach ($imagens as $index => $img): ?>
                    <img src="<?= htmlspecialchars($img) ?>" 
                         alt="Imagem <?= $index + 1 ?>"
                         class="miniatura <?= $index == 0 ? 'ativa' : '' ?>"
                         onclick="trocarImagem('<?= htmlspecialchars($img) ?>', this)"
                         onerror="this.onerror=null; this.src='img/sem-foto.jpg';">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- COLUNA 2: INFORMAÇÕES -->
            <div class="info-container">
                <!-- Badges -->
                <div>
                    <span class="badge-tipo">
                        <?= $tipo == 'produto' ? 'PRODUTO' : 'LIVRO' ?>
                    </span>
                    <?php if (($tipo == 'produto' && $item['status'] == 'doacao') || ($tipo == 'livro' && empty($item['preco']))): ?>
                    <span class="badge-doacao">DOAÇÃO</span>
                    <?php endif; ?>
                </div>
                
                <!-- Título -->
                <h1><?= $titulo_pagina ?></h1>
                
                <!-- Descrição -->
                <?php if (!empty($item['descricao'])): ?>
                <div class="descricao-box">
                    <h3><i class="fas fa-align-left"></i> Descrição</h3>
                    <p><?= nl2br(htmlspecialchars($item['descricao'])) ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Estado de conservação -->
                <?php if (!empty($item['estadoconservacao'])): ?>
                <div class="detalhe-item">
                    <strong><i class="fas fa-star"></i> Estado de conservação</strong>
                    <span><?= htmlspecialchars($item['estadoconservacao']) ?></span>
                </div>
                <?php endif; ?>
                
                <!-- DETALHES ESPECÍFICOS -->
                <div class="detalhes-grid">
                    <?php if ($tipo == 'livro'): ?>
                        <?php if (!empty($item['autor'])): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-user"></i> Autor</strong>
                            <span><?= htmlspecialchars($item['autor']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['editora'])): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-building"></i> Editora</strong>
                            <span><?= htmlspecialchars($item['editora']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['ano'])): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-calendar"></i> Ano</strong>
                            <span><?= htmlspecialchars($item['ano']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['ISBN'])): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-barcode"></i> ISBN</strong>
                            <span><?= htmlspecialchars($item['ISBN']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['idioma'])): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-language"></i> Idioma</strong>
                            <span><?= htmlspecialchars($item['idioma']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['pag'])): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-file"></i> Páginas</strong>
                            <span><?= htmlspecialchars($item['pag']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['preco']) && $item['preco'] > 0): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-tag"></i> Preço</strong>
                            <span style="color: #27ae60; font-weight: bold;">
                                R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                    <?php else: // PRODUTO ?>
                        <?php if (!empty($item['preco']) && $item['preco'] > 0 && $item['status'] != 'doacao'): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-tag"></i> Preço</strong>
                            <span style="color: #27ae60; font-weight: bold;">
                                R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['peso'])): ?>
                        <div class="detalhe-item">
                            <strong><i class="fas fa-weight"></i> Peso</strong>
                            <span><?= htmlspecialchars($item['peso']) ?> g</span>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <!-- MARKETPLACES -->
                <?php if (!empty($marketplaces_ativos)): ?>
                <div class="marketplaces-section">
                    <h3><i class="fas fa-store"></i> Disponível em:</h3>
                    <div class="marketplaces-grid">
                        <?php foreach ($marketplaces_ativos as $nome => $mp): ?>
                        <a href="<?= htmlspecialchars($mp['url']) ?>" 
                           target="_blank" 
                           class="marketplace-link"
                           title="Ver no <?= htmlspecialchars($nome) ?>">
                            <?php if (file_exists('img/' . $mp['logo'])): ?>
                            <img src="img/<?= htmlspecialchars($mp['logo']) ?>" alt="<?= htmlspecialchars($nome) ?>">
                            <?php else: ?>
                            <i class="fas fa-external-link-alt" style="font-size: 2rem; color: #3498db;"></i>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($nome) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script>
        // FUNÇÃO PARA TROCAR IMAGENS NO CARROSSEL
        function trocarImagem(src, elemento) {
            // Trocar imagem principal
            document.getElementById('imagem-principal').src = src;
            
            // Atualizar miniaturas ativas
            document.querySelectorAll('.miniatura').forEach(min => {
                min.classList.remove('ativa');
            });
            elemento.classList.add('ativa');
        }
        
        // VOLTAR AO TOPO AO CARREGAR
        window.scrollTo(0, 0);
    </script>
</body>
</html>