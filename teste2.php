<?php
// check-structure.php
require_once 'config.php';

echo "<h2>Verificando estrutura das tabelas</h2>";

try {
    // Verificar colunas da tabela GERAL
    echo "<h3>Tabela GERAL:</h3>";
    $stmt = $pdo->query("DESCRIBE geral");
    $colunas = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>";
    foreach($colunas as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar colunas da tabela LIVROS
    echo "<h3>Tabela LIVROS:</h3>";
    $stmt = $pdo->query("DESCRIBE livros");
    $colunas = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>";
    foreach($colunas as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar valores únicos na coluna 'vendido' da tabela livros
    echo "<h3>Valores na coluna 'vendido' da tabela LIVROS:</h3>";
    $stmt = $pdo->query("SELECT DISTINCT vendido, COUNT(*) as total FROM livros GROUP BY vendido");
    $valores = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>";
    foreach($valores as $valor) {
        echo "<tr>";
        echo "<td>" . ($valor['vendido'] ? 'TRUE' : 'FALSE') . "</td>";
        echo "<td>{$valor['total']} itens</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>