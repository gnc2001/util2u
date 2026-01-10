<?php
// test-search.php
require_once 'config.php';

echo "<h2>Teste de Pesquisa</h2>";

// Testar conexão
try {
    echo "✅ Conexão OK<br>";
    
    // Testar uma busca simples na tabela geral
    $sql = "SELECT COUNT(*) as total FROM geral WHERE status = 1";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch();
    echo "Itens ativos na tabela 'geral': " . $result['total'] . "<br>";
    
    // Testar uma busca simples na tabela livros
    $sql = "SELECT COUNT(*) as total FROM livros WHERE status = 1";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch();
    echo "Itens ativos na tabela 'livros': " . $result['total'] . "<br>";
    
    // Testar busca com termo específico
    $termo = "teste"; // Substitua por um termo que você sabe que existe
    $sql = "SELECT titulo_geral FROM geral WHERE titulo_geral LIKE ? LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$termo%"]);
    $resultados = $stmt->fetchAll();
    
    echo "<h3>Resultados para '$termo' em 'geral':</h3>";
    if (count($resultados) > 0) {
        foreach($resultados as $row) {
            echo "- " . $row['titulo_geral'] . "<br>";
        }
    } else {
        echo "Nenhum resultado encontrado<br>";
    }
    
    // Testar a query do busca.php
    echo "<h3>Testando query do busca.php:</h3>";
    $sqlTeste = "SELECT 
        g.id_geral as id, 
        g.titulo_geral as titulo, 
        g.foto_principal,
        g.marketplaces,
        'coisa' as tipo,
        g.data_cadastro
    FROM geral g
    WHERE g.status = 1
    LIMIT 5";
    
    $stmt = $pdo->query($sqlTeste);
    $testResults = $stmt->fetchAll();
    
    echo "Primeiros 5 resultados:<br>";
    echo "<pre>";
    print_r($testResults);
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>