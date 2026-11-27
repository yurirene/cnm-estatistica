<script>
(function () {
    const root = document.getElementById('painel-planejamento');
    if (!root) return;

    function navegar(ano, regiao) {
        const url = new URL(window.location.href);
        if (ano) {
            url.searchParams.set('ano', ano);
        }
        if (regiao) {
            url.searchParams.set('regiao', regiao);
        } else {
            url.searchParams.delete('regiao');
        }
        window.location.href = url.toString();
    }

    document.querySelectorAll('.pe-year-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const ano = btn.getAttribute('data-ano');
            const regiaoSelect = document.getElementById('pe-regiao');
            const regiao = regiaoSelect ? regiaoSelect.value : (root.getAttribute('data-regiao') || '');
            navegar(ano, regiao);
        });
    });

    const filtrarBtn = document.getElementById('pe-filtrar');
    const regiaoSelect = document.getElementById('pe-regiao');
    if (filtrarBtn && regiaoSelect) {
        filtrarBtn.addEventListener('click', function () {
            navegar(root.getAttribute('data-ano'), regiaoSelect.value);
        });
        regiaoSelect.addEventListener('change', function () {
            navegar(root.getAttribute('data-ano'), regiaoSelect.value);
        });
    }
})();
</script>
