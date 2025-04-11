window.loadCompanies = function (page = 1) {
    const container = document.getElementById("company-table-container");
    const pagination = document.getElementById("pagination-container");

    container.innerHTML = "A carregar empresas...";
    pagination.innerHTML = "";

    fetch(`pages/admin/empresas.php?fetch=1&page=${page}`)
        .then(res => res.json())
        .then(data => {
            if (!data.companies.length) {
                container.innerHTML = "<p>Nenhuma empresa encontrada.</p>";
                return;
            }

            const html = data.companies.map(company => `
                <div class="company-row ${company.COMPANY_STATUS !== 'A' ? 'inactive' : ''}">
                    <div class="company-info">
                        <strong>${company.COMPANY_NAME}</strong><br>
                        <small>${company.COMPANY_EMAIL}</small><br>
                        <small><a href="${company.COMPANY_SITE}" target="_blank">${company.COMPANY_SITE}</a></small>
                    </div>
                    <div class="company-actions">
                        <button class="edit_button" onclick='openEditModal(${JSON.stringify(company)})'>Editar</button>
                        <button class="delete_button" onclick="toggleCompanyStatus('${company.COMPANY_ID}')">
                            ${company.COMPANY_STATUS === 'A' ? 'Inativar' : 'Ativar'}
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
                    btn.addEventListener("click", () => loadCompanies(i));
                    pagination.appendChild(btn);
                }
            }
        })
        .catch(err => {
            container.innerHTML = "<p>Erro ao carregar empresas.</p>";
            console.error(err);
        });
};

function openEditModal(company) {
    document.getElementById("edit_company_id").value = company.COMPANY_ID;
    document.getElementById("edit_company_name").value = company.COMPANY_NAME;
    document.getElementById("edit_company_email").value = company.COMPANY_EMAIL;
    document.getElementById("edit_company_site").value = company.COMPANY_SITE;
    document.getElementById("editCompanyModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editCompanyModal").style.display = "none";
}

document.getElementById("editCompanyForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append("action", "update");

    fetch("pages/admin/empresas.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            loadCompanies();
        } else {
            alert("Erro ao atualizar empresa.");
        }
    })
    .catch(err => {
        console.error("Erro:", err);
        alert("Erro ao atualizar.");
    });
});

function toggleCompanyStatus(companyId) {
    const formData = new FormData();
    formData.append("action", "toggle");
    formData.append("COMPANY_ID", companyId);

    fetch("pages/admin/empresas.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadCompanies();
        } else {
            alert("Erro ao alterar estado da empresa.");
        }
    })
    .catch(err => {
        console.error("Erro:", err);
        alert("Erro ao alterar estado.");
    });
}

document.addEventListener("DOMContentLoaded", () => {
    loadCompanies();
});
