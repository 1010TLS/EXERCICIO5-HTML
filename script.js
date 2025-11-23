document.addEventListener("DOMContentLoaded", function () {
    const botoes = document.querySelectorAll(".confirmar-exclusao");

    botoes.forEach(btn => {
        btn.addEventListener("click", function (e) {
            if (!confirm("Excluir este contato?")) {
                e.preventDefault();
            }
        });
    });
});
