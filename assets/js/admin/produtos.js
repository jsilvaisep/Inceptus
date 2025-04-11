window.loadProducts = function (page = 1) {
    const container = document.getElementById("product-table-container");
    const pagination = document.getElementById("pagination-container");

    container.innerHTML = "A carregar produtos...";
    pagination.innerHTML = "";

    fetch(`pages/admin/produtos.php?fetch=1&page=${page}`)
        .then(res => res.json())
        .then(data => {
            if (!data.products.length) {
                container.innerHTML = "<p>Nenhum produto encontrado.</p>";
                return;
            }

            const html = data.products.map(product => `
                <div class="product-row ${product.PRODUCT_STATUS !== 'A' ? 'inactive' : ''}">
                    <div class="product-info">
                        <strong>${product.PRODUCT_NAME}</strong><br>
                        <small><b>Empresa:</b> ${product.COMPANY_NAME}</small><br>
                        <small>${product.PRODUCT_DESCRIPTION}</small>
                    </div>
                    <div class="product-actions">
                        <button class="edit_button" onclick='openEditModal(${JSON.stringify(product)})'>Editar</button>
                        <button class="delete_button" onclick="toggleProductStatus('${product.PRODUCT_ID}')">
                            ${product.PRODUCT_STATUS === 'A' ? 'Inativar' : 'Ativar'}
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
                    btn.addEventListener("click", () => loadProducts(i));
                    pagination.appendChild(btn);
                }
            }
        })
        .catch(err => {
            container.innerHTML = "<p>Erro ao carregar produtos.</p>";
            console.error(err);
        });
};

function openEditModal(product) {
    document.getElementById("edit_product_id").value = product.PRODUCT_ID;
    document.getElementById("edit_product_name").value = product.PRODUCT_NAME;
    document.getElementById("edit_product_description").value = product.PRODUCT_DESCRIPTION;
    document.getElementById("editProductModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editProductModal").style.display = "none";
}

document.getElementById("editProductForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append("action", "update");

    fetch("pages/admin/produtos.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            loadProducts();
        } else {
            alert("Erro ao atualizar produto.");
        }
    })
    .catch(err => {
        console.error("Erro:", err);
        alert("Erro ao atualizar.");
    });
});

function toggleProductStatus(productId) {
    const formData = new FormData();
    formData.append("action", "toggle");
    formData.append("PRODUCT_ID", productId);

    fetch("pages/admin/produtos.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadProducts();
        } else {
            alert("Erro ao alterar estado do produto.");
        }
    })
    .catch(err => {
        console.error("Erro:", err);
        alert("Erro ao alterar estado.");
    });
}

document.addEventListener("DOMContentLoaded", () => {
    loadProducts();
});
