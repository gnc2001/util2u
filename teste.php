<?php
require_once 'config.php';

echo "<h2>Teste de Conexão e Estrutura</h2>";

// Testar conexão
try {
    echo "✅ Conexão com banco estabelecida!<br><br>";
    
    // Listar tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tabelas encontradas:</h3>";
    echo "<ul>";
    foreach($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Ver estrutura de uma tabela exemplo
    echo "<h3>Estrutura da tabela 'geral':</h3>";
    $stmt = $pdo->query("DESCRIBE geral");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>