<?php
// busca.php - VERSÃO FINAL com condições reais
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? 'produtos';

try {
    switch($action) {
        case 'filtros':
            // Buscar categorias
            $stmtCategorias = $pdo->query("SELECT id_cat, nome_cat FROM categorias ORDER BY nome_cat");
            $categorias = $stmtCategorias->fetchAll();
            
            // Buscar estantes
            $stmtEstantes = $pdo->query("SELECT id_est, nome_est FROM estantes ORDER BY nome_est");
            $estantes = $stmtEstantes->fetchAll();
            
            echo json_encode([
                'categorias' => $categorias,
                'estantes' => $estantes
            ]);
            break;
            
        case 'produtos':
        default:
            $busca = trim($_GET['busca'] ?? '');
            $categoria = $_GET['categoria'] ?? '';
            $estante = $_GET['estante'] ?? '';
            $ordenacao = $_GET['ordenacao'] ?? 'recentes';
            
            // Query para COISAS (tabela geral)
            // GERAL: Mostrar apenas onde status = 1 (ativo)
            $sqlCoisas = "SELECT 
                g.id_geral as id, 
                g.titulo_geral as titulo, 
                g.foto_principal,
                g.marketplaces,
                'coisa' as tipo,
                g.data_cadastro,
                g.descricao,
                g.preco,
                g.condicao,
                c.nome_cat as categoria_nome
            FROM geral g
            LEFT JOIN categorias c ON g.categoria_id = c.id_cat
            WHERE g.status = 1"; // Apenas itens ativos
            
            $params = [];
            
            if (!empty($busca)) {
                $sqlCoisas .= " AND (g.titulo_geral LIKE ? OR g.descricao LIKE ?)";
                $searchTerm = "%$busca%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($categoria) && $categoria !== '') {
                $sqlCoisas .= " AND g.categoria_id = ?";
                $params[] = (int)$categoria;
            }
            
            // Query para LIVROS
            // LIVROS: Mostrar apenas onde vendido = FALSE (não vendido/doado)
            $sqlLivros = "SELECT 
                l.id_livro as id, 
                l.titulo_livro as titulo, 
                l.foto_principal,
                l.marketplaces,
                'livro' as tipo,
                l.data_cadastro,
                l.descricao,
                l.preco,
                l.condicao,
                l.autor,
                l.editora,
                l.ano,
                e.nome_est as estante_nome
            FROM livros l
            LEFT JOIN estantes e ON l.estante_id = e.id_est
            WHERE l.vendido = FALSE"; // Apenas livros não vendidos/doados
            
            $livrosParams = [];
            
            if (!empty($busca)) {
                $sqlLivros .= " AND (l.titulo_livro LIKE ? OR l.autor LIKE ? OR l.descricao LIKE ?)";
                $searchTerm = "%$busca%";
                $livrosParams[] = $searchTerm;
                $livrosParams[] = $searchTerm;
                $livrosParams[] = $searchTerm;
            }
            
            if (!empty($estante) && $estante !== '') {
                $sqlLivros .= " AND l.estante_id = ?";
                $livrosParams[] = (int)$estante;
            }
            
            // Combinação das queries
            $sql = "($sqlCoisas) UNION ($sqlLivros)";
            
            // Combinar parâmetros
            $allParams = array_merge($params, $livrosParams);
            
            // Ordenação
            switch($ordenacao) {
                case 'antigos':
                    $sql .= " ORDER BY data_cadastro ASC";
                    break;
                case 'titulo_asc':
                    $sql .= " ORDER BY titulo ASC";
                    break;
                case 'titulo_desc':
                    $sql .= " ORDER BY titulo DESC";
                    break;
                case 'recentes':
                default:
                    $sql .= " ORDER BY data_cadastro DESC";
            }
            
            // Limitar resultados
            $sql .= " LIMIT 100";
            
            // Executar query
            $stmt = $pdo->prepare($sql);
            $stmt->execute($allParams);
            $produtos = $stmt->fetchAll();
            
            echo json_encode($produtos);
            break;
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}
?>