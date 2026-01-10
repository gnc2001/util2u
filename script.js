// script.js - ATUALIZADO
document.addEventListener('DOMContentLoaded', function() {
    // ... (código anterior permanece igual até a função criarCardProduto)
    
    // Criar HTML do card do produto - VERSÃO ATUALIZADA
    function criarCardProduto(produto) {
        // Mapeamento dos marketplaces para logos
        const marketplacesLogos = {
            'olx1': 'logoolx1pq.png',
            'olx2': 'logoolx2pq.png',
            'ml': 'logoml.png',
            'enjoei': 'logoenjoei.png',
            'estante': 'logoestante.png',
            'amazon': 'logoamazon.png'
        };
        
        // Processar marketplaces
        let logosHTML = '';
        if (produto.marketplaces) {
            const marketplacesArray = produto.marketplaces.split(',');
            marketplacesArray.forEach(marketplace => {
                const cleanMarketplace = marketplace.trim();
                if (cleanMarketplace && marketplacesLogos[cleanMarketplace]) {
                    logosHTML += `<img src="img/${marketplacesLogos[cleanMarketplace]}" 
                                     alt="${cleanMarketplace}" 
                                     class="marketplace-logo"
                                     title="${cleanMarketplace.toUpperCase()}">`;
                }
            });
        }
        
        // Tratar imagem ausente
        const imagemSrc = produto.foto_principal 
            ? produto.foto_principal 
            : 'img/placeholder.jpg';
        
        // Informação adicional (preço ou autor)
        let infoExtra = '';
        if (produto.tipo === 'livro' && produto.autor) {
            infoExtra = `<p class="product-author">${produto.autor}</p>`;
        } else if (produto.preco) {
            infoExtra = `<p class="product-price">R$ ${parseFloat(produto.preco).toFixed(2)}</p>`;
        }
        
        return `
            <div class="product-card" data-id="${produto.id}" data-tipo="${produto.tipo}">
                <div class="product-image">
                    <img src="${imagemSrc}" alt="${produto.titulo}" 
                         onerror="this.src='img/placeholder.jpg'">
                </div>
                <div class="product-info">
                    <h3 class="product-title">${produto.titulo}</h3>
                    ${infoExtra}
                    ${produto.categoria_nome ? `<span class="product-category">${produto.categoria_nome}</span>` : ''}
                    ${produto.estante_nome ? `<span class="product-estante">${produto.estante_nome}</span>` : ''}
                    <div class="marketplace-logos">
                        ${logosHTML}
                    </div>
                </div>
            </div>
        `;
    }
});