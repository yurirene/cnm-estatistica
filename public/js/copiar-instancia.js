/**
 * Função para copiar o ID da instância do usuário para a área de transferência
 */
function copiarIdInstancia() {
    const button = event.target.closest('a');
    const instanciaId = button.getAttribute('data-instancia-id');
    
    if (instanciaId) {
        const textoParaCopiar = `${instanciaId}`;
        
        // Tenta usar a API moderna do clipboard
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textoParaCopiar).then(function() {
                mostrarMensagemSucesso();
            }).catch(function(err) {
                // Fallback para navegadores mais antigos
                copiarComFallback(textoParaCopiar);
            });
        } else {
            // Fallback para navegadores mais antigos
            copiarComFallback(textoParaCopiar);
        }
    } else {
        mostrarMensagemErro();
    }
}

/**
 * Função de fallback para copiar texto usando método antigo
 */
function copiarComFallback(texto) {
    const textArea = document.createElement('textarea');
    textArea.value = texto;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        mostrarMensagemSucesso();
    } catch (err) {
        mostrarMensagemErro();
    }
    
    document.body.removeChild(textArea);
}

/**
 * Mostra mensagem de sucesso
 */
function mostrarMensagemSucesso() {
    if (typeof iziToast !== 'undefined') {
        iziToast.success({
            title: 'Sucesso!',
            message: 'ID da instância copiado para a área de transferência!',
            position: 'topRight',
            timeout: 3000
        });
    } else {
        alert('ID da instância copiado para a área de transferência!');
    }
}

/**
 * Mostra mensagem de erro
 */
function mostrarMensagemErro() {
    if (typeof iziToast !== 'undefined') {
        iziToast.error({
            title: 'Erro!',
            message: 'Usuário não possui instância associada.',
            position: 'topRight',
            timeout: 3000
        });
    } else {
        alert('Erro: Usuário não possui instância associada.');
    }
} 