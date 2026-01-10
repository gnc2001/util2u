<?php
// detalhe.php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$id = $_GET['id'] ?? 0;
$tipo = $_GET['tipo'] ?? '';

if (!$id || !in_array($tipo, ['coisa', 'livro'])) {
    echo json_encode(['error' => 'Parâmetros inválidos']);
    exit;
}

try {
    if ($tipo === 'coisa') {
        $sql = "SELECT 
                g.id_geral as id,
                g.titulo_geral as titulo,
                g.descricao,
                g.foto_principal,
                g.outras_fotos,
                g.preco,
                g.condicao,
                g.marketplaces,
                g.data_cadastro,
                c.nome_cat as categoria_nome
            FROM geral g
            LEFT JOIN categorias c ON g.categoria_id = c.id_cat
            WHERE g.id_geral = ? AND g.status = 1";
    } else {
        $sql = "SELECT 
                l.id_livro as id,
                l.titulo_livro as titulo,
                l.descricao,
                l.foto_principal,
                l.outras_fotos,
                l.preco,
                l.condicao,
                l.marketplaces,
                l.data_cadastro,
                l.autor,
                l.editora,
                l.ano,
                l.isbn,
                e.nome_est as estante_nome
            FROM livros l
            LEFT JOIN estantes e ON l.estante_id = e.id_est
            WHERE l.id_livro = ? AND l.status = 1";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
    
    if ($produto) {
        $produto['tipo'] = $tipo;
        echo json_encode($produto);
    } else {
        echo json_encode(['error' => 'Produto não encontrado']);
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>