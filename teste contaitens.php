<?php
// count-items.php
require_once 'config.php';

echo "<h2>Contagem de itens disponíveis</h2>";

try {
    // Contar itens em GERAL com status = 1
    $sql = "SELECT COUNT(*) as total FROM geral WHERE status = 1";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch();
    echo "✅ Itens ativos em GERAL: " . $result['total'] . "<br>";
    
    // Contar itens em LIVROS com vendido = FALSE
    $sql = "SELECT COUNT(*) as total FROM livros WHERE vendido = FALSE";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch();
    echo "✅ Livros não vendidos em LIVROS: " . $result['total'] . "<br>";
    
    // Ver alguns exemplos
    echo "<h3>Exemplos de GERAL ativos:</h3>";
    $sql = "SELECT id_geral, titulo_geral, marketplaces FROM geral WHERE status = 1 LIMIT 5";
    $stmt = $pdo->query($sql);
    $exemplos = $stmt->fetchAll();
    
    echo "<ul>";
    foreach($exemplos as $item) {
        echo "<li>{$item['titulo_geral']} (Marketplaces: {$item['marketplaces']})</li>";
    }
    echo "</ul>";
    
    echo "<h3>Exemplos de LIVROS não vendidos:</h3>";
    $sql = "SELECT id_livro, titulo_livro, marketplaces FROM livros WHERE vendido = FALSE LIMIT 5";
    $stmt = $pdo->query($sql);
    $exemplos = $stmt->fetchAll();
    
    echo "<ul>";
    foreach($exemplos as $item) {
        echo "<li>{$item['titulo_livro']} (Marketplaces: {$item['marketplaces']})</li>";
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>