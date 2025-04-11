window.loadNews = function (page = 1) {
    const container = document.getElementById("news-table-container");
    const pagination = document.getElementById("pagination-container");

    container.innerHTML = "A carregar notícias...";
    pagination.innerHTML = "";

    fetch(`pages/admin/noticias.php?fetch=1&page=${page}`)
        .then(res => res.json())
        .then(data => {
            if (!data.news.length) {
                container.innerHTML = "<p>Nenhuma notícia encontrada.</p>";
                return;
            }

            const html = data.news.map(post => `
                <div class="news-row ${post.POST_STATUS !== 'A' ? 'inactive' : ''}">
                    <div class="news-info">
                        <strong>${post.COMPANY_NAME}</strong><br>
                        <small>${post.POST_CONTENT}</small>
                    </div>
                    <div class="news-actions">
                        <button class="delete_button" onclick="toggleNewsStatus('${post.POST_ID}')">
                            ${post.POST_STATUS === 'A' ? 'Inativar' : 'Ativar'}
                        </button>
                    </div>
                </div>
            `).join("");

            container.innerHTML = html;

            if (data.pages > 1) {
                for (let i = 1; i <= data.pages; i++) {
                    const btn = document.createElement("button");
                    btn.className = `page-btn ${i === data.page ? 'active' : ''}`;
                    btn.textContent = i;
                    btn.addEventListener("click", () => loadNews(i));
                    pagination.appendChild(btn);
                }
            }
        })
        .catch(err => {
            container.innerHTML = "<p>Erro ao carregar notícias.</p>";
            console.error(err);
        });
};

function toggleNewsStatus(postId) {
    const formData = new FormData();
    formData.append("action", "toggle");
    formData.append("POST_ID", postId);

    fetch("pages/admin/noticias.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadNews();
        } else {
            alert("Erro ao alterar estado da notícia.");
        }
    })
    .catch(err => {
        console.error("Erro:", err);
        alert("Erro ao alterar estado.");
    });
}

// Carrega ao abrir a página
document.addEventListener("DOMContentLoaded", () => {
    loadNews();
});
