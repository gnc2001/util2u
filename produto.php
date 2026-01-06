<?php
require_once 'config.php';

$tipo = $_GET['tipo'] ?? '';
$id = $_GET['id'] ?? 0;

if (empty($tipo) || empty($id)) {
    header('Location: index.php');
    exit;
}

// Buscar item específico
if ($tipo == 'livro') {
    $query = "SELECT * FROM livros WHERE ID = :id";
    $tabela = 'livros';
    $campo_id = 'ID';
} else {
    $query = "SELECT * FROM geral WHERE id = :id";
    $tabela = 'geral';
    $campo_id = 'id';
}

$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header('Location: index.php');
    exit;
}

// Buscar imagens adicionais
if ($tipo == 'livro') {
    $imagens_query = "SELECT caminho FROM imagem_livros WHERE id_livro = :id";
} else {
    $imagens_query = "SELECT caminho FROM imagens WHERE item_id = :id";
}

$imgs_stmt = $pdo->prepare($imagens_query);
$imgs_stmt->execute([':id' => $id]);
$imagens = $imgs_stmt->fetchAll(PDO::FETCH_COLUMN);

// Adicionar a imagem principal no início do array
if (!empty($item['icone'])) {
    array_unshift($imagens, $item['icone']);
}

// Marketplaces com links
$marketplaces = [
    'olx1' => ['logo' => 'logoolx1pq.png', 'url' => $item['olx1'] ?? ''],
    'olx2' => ['logo' => 'logoolx2pq.png', 'url' => $item['olx2'] ?? ''],
    'ml1' => ['logo' => 'logoml1pq.png', 'url' => $item['ml1'] ?? ''],
    'ml2' => ['logo' => 'logoml2pq.png', 'url' => $item['ml2'] ?? ''],
    'ev' => ['logo' => 'logoevpq.png', 'url' => $item['ev'] ?? ''],
    'enjoei' => ['logo' => 'logoenjoeipq.png', 'url' => $item['enjoei'] ?? ''],
    'amazon' => ['logo' => 'logoamazonpq.png', 'url' => $item['amazon'] ?? ''],
    'shopee' => ['logo' => 'logoshopeeepq.png', 'url' => $item['shopee'] ?? '']
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item['titulo'] ?? $item['nome']) ?> - Grande Desapego</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos específicos para página de produto */
        .produto-detalhe {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 5%;
        }
        
        .voltar-link {
            display: inline-block;
            margin-bottom: 1.5rem;
            color: #3498db;
            text-decoration: none;
            font-size: 1.1rem;
        }
        
        .voltar-link:hover {
            text-decoration: underline;
        }
        
        .produto-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .produto-header {
            display: flex;
            flex-wrap: wrap;
            gap: 3rem;
            margin-bottom: 2rem;
        }
        
        .carrossel-container {
            flex: 1;
            min-width: 300px;
            max-width: 500px;
        }
        
        .carrossel-principal {
            height: 400px;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .carrossel-principal img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .carrossel-miniaturas {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
        
        .miniatura {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.3s;
            flex-shrink: 0;
        }
        
        .miniatura.ativo, .miniatura:hover {
            opacity: 1;
            border: 2px solid #3498db;
        }
        
        .miniatura img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .info-container {
            flex: 1;
            min-width: 300px;
        }
        
        .info-container h1 {
            font-size: 2.2rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .preco-grande {
            font-size: 2.5rem;
            font-weight: bold;
            color: #27ae60;
            margin: 1rem 0;
        }
        
        .preco-doacao-grande {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .descricao-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            line-height: 1.8;
        }
        
        .marketplace-links {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #eee;
        }
        
        .mp-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #333;
            transition: transform 0.3s;
        }
        
        .mp-link:hover {
            transform: translateY(-5px);
        }
        
        .mp-logo {
            width: 60px;
            height: 60px;
            margin-bottom: 0.5rem;
        }
        
        .mp-nome {
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .produto-header {
                flex-direction: column;
                gap: 2rem;
            }
            
            .carrossel-container {
                max-width: 100%;
            }
            
            .carrossel-principal {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho reduzido -->
    <header class="faixa-1" style="padding: 1rem 5%;">
        <div class="logo-container" style="flex-direction: row; justify-content: center; gap: 1rem;">
            <img src="img/logo.png" alt="Logo" class="logo" style="max-width: 60px;">
            <h1 style="font-size: 1.8rem;">Grande Desapego</h1>
        </div>
    </header>
    
    <main class="produto-detalhe">
        <a href="index.php" class="voltar-link">
            <i class="fas fa-arrow-left"></i> Voltar para a lista
        </a>
        
        <div class="produto-container">
            <div class="produto-header">
                <!-- Carrossel -->
                <div class="carrossel-container">
                    <div class="carrossel-principal">
                        <?php if (!empty($imagens)): ?>
                            <img id="imagem-principal" src="<?= htmlspecialchars($imagens[0]) ?>" 
                                 alt="<?= htmlspecialchars($item['titulo'] ?? $item['nome']) ?>"
                                 onerror="this.src='img/sem-foto.jpg'">
                        <?php else: ?>
                            <img id="imagem-principal" src="img/sem-foto.jpg" alt="Sem foto">
                        <?php endif; ?>
                    </div>
                    
                    <?php if (count($imagens) > 1): ?>
                    <div class="carrossel-miniaturas">
                        <?php foreach ($imagens as $index => $img): ?>
                            <div class="miniatura <?= $index == 0 ? 'ativo' : '' ?>" 
                                 onclick="trocarImagem('<?= htmlspecialchars($img) ?>', this)">
                                <img src="<?= htmlspecialchars($img) ?>" 
                                     onerror="this.src='img/sem-foto.jpg'">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Informações -->
                <div class="info-container">
                    <h1><?= htmlspecialchars($item['titulo'] ?? $item['nome']) ?></h1>
                    
                    <?php if ($item['status'] == 'doacao' || ($tipo == 'livro' && empty($item['preco']))): ?>
                        <div class="preco-doacao-grande">DOAÇÃO - GRÁTIS</div>
                    <?php else: ?>
                        <div class="preco-grande">R$ <?= number_format($item['preco'], 2, ',', '.') ?></div>
                    <?php endif; ?>
                    
                    <!-- Informações específicas para livros -->
                    <?php if ($tipo == 'livro'): ?>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($item['autor'] ?? 'Não informado') ?></p>
                        <p><strong>Editora:</strong> <?= htmlspecialchars($item['editora'] ?? 'Não informado') ?></p>
                        <p><strong>Ano:</strong> <?= $item['ano'] ?? 'Não informado' ?></p>
                        <p><strong>ISBN:</strong> <?= htmlspecialchars($item['ISBN'] ?? 'Não informado') ?></p>
                    <?php endif; ?>
                    
                    <!-- Estado de conservação -->
                    <?php if (!empty($item['estadoconservacao'])): ?>
                        <p><strong>Estado:</strong> <?= htmlspecialchars($item['estadoconservacao']) ?></p>
                    <?php endif; ?>
                    
                    <!-- Descrição -->
                    <?php if (!empty($item['descricao'])): ?>
                        <div class="descricao-box">
                            <h3>Descrição</h3>
                            <p><?= nl2br(htmlspecialchars($item['descricao'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Links para marketplaces -->
                    <div class="marketplace-links">
                        <h3 style="width: 100%; margin-bottom: 1rem;">Disponível em:</h3>
                        <?php foreach ($marketplaces as $key => $mp): 
                            if (!empty($mp['url'])): ?>
                            <a href="<?= htmlspecialchars($mp['url']) ?>" 
                               target="_blank" 
                               class="mp-link"
                               title="Ver no <?= strtoupper($key) ?>">
                                <img src="img/<?= $mp['logo'] ?>" 
                                     alt="<?= strtoupper($key) ?>" 
                                     class="mp-logo">
                                <span class="mp-nome"><?= strtoupper($key) ?></span>
                            </a>
                            <?php endif;
                        endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        // Carrossel simples
        function trocarImagem(src, elemento) {
            document.getElementById('imagem-principal').src = src;
            
            // Atualizar miniaturas ativas
            document.querySelectorAll('.miniatura').forEach(min => {
                min.classList.remove('ativo');
            });
            elemento.classList.add('ativo');
        }
    </script>
</body>
</html>