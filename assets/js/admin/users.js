window.loadUsers = function (page = 1) {
    const container = document.getElementById("user-table-container");
    const pagination = document.getElementById("pagination-container");

    container.innerHTML = "A carregar utilizadores...";
    pagination.innerHTML = "";

    fetch(`pages/admin/users.php?fetch=1&page=${page}`)
        .then(res => res.json())
        .then(data => {
            if (!data.users.length) {
                container.innerHTML = "<p>Nenhum utilizador encontrado.</p>";
                return;
            }

            const html = data.users.map(user => `
                <div class="user-row ${user.USER_STATUS !== 'A' ? 'inactive' : ''}">
                    <div class="user-info">
                        <img src="${user.IMG_URL || 'assets/img/default-avatar.png'}" alt="Foto" class="user-avatar">
                        <div>
                            <strong>${user.USER_NAME}</strong><br>
                            <small>${user.USER_LOGIN}</small><br>
                            <small>${user.USER_EMAIL}</small>
                        </div>
                    </div>
                    <div class="user-actions">
                        <button class="edit_button" onclick='openEditModal(${JSON.stringify(user)})'>Editar</button>
                        <button class="delete_button" onclick="toggleUserStatus('${user.USER_ID}')">
                            ${user.USER_STATUS === 'A' ? 'Inativar' : 'Ativar'}
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
                    btn.addEventListener("click", () => loadUsers(i));
                    pagination.appendChild(btn);
                }
            }
        })
        .catch(err => {
            container.innerHTML = "<p>Erro ao carregar utilizadores.</p>";
            console.error(err);
        });
};

function openEditModal(user) {
    document.getElementById("edit_user_id").value = user.USER_ID;
    document.getElementById("edit_user_login").value = user.USER_LOGIN;
    document.getElementById("edit_user_name").value = user.USER_NAME;
    document.getElementById("edit_user_email").value = user.USER_EMAIL;
    document.getElementById("editUserModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editUserModal").style.display = "none";
}

document.getElementById("editUserForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append("action", "update");

    fetch("pages/admin/users.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            loadUsers();
        } else {
            alert("Erro ao atualizar utilizador.");
        }
    })
    .catch(err => {
        console.error("Erro:", err);
        alert("Erro ao atualizar.");
    });
});

function toggleUserStatus(userId) {
    const formData = new FormData();
    formData.append("action", "toggle");
    formData.append("USER_ID", userId);

    fetch("pages/admin/users.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadUsers();
        } else {
            alert("Erro ao alterar estado do utilizador.");
        }
    })
    .catch(err => {
        console.error("Erro:", err);
        alert("Erro ao alterar estado.");
    });
}
