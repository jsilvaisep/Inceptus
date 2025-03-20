document.addEventListener("DOMContentLoaded", () => {
    console.log("SPA carregada!");
    document.getElementById("year").textContent = new Date().getFullYear();
    loadPage('home'); // Carrega a página inicial ao abrir o site
});

/**
 * Carregar dinamicamente páginas dentro do `<main>`
 */
function loadPage(page) {
    const appContent = document.getElementById("app-content");

    const pages = {
        home: `
            <section id="intro">
                <h1>Bem-vindo ao Inceptus</h1>
                <p>Descubra e avalie produtos inovadores!</p>
            </section>

            <section id="search">
                <div class="search-container">
                    <input type="text" placeholder="Pesquisar produtos...">
                    <button class="btn">Pesquisar</button>
                </div>
            </section>

            <section id="products">
                <h2>Produtos em Destaque</h2>
                <div class="product-list">
                    <article class="product-card">
                        <h3>Produto 1</h3>
                        <p>Descrição breve do produto...</p>
                        <button class="btn">Ver mais</button>
                    </article>
                    <article class="product-card">
                        <h3>Produto 2</h3>
                        <p>Outra descrição breve...</p>
                        <button class="btn">Ver mais</button>
                    </article>
                </div>
            </section>
        `,
        login: loadAuthPage("login"),
        register: loadAuthPage("register"),
        about: "<h1>Sobre Nós</h1><p>Informações sobre a empresa...</p>",
        contact: "<h1>Contacto</h1><p>Fale connosco pelo email: contacto@inceptus.com</p>",
        privacy: "<h1>Política de Privacidade</h1><p>Detalhes da nossa política.</p>",
        terms: "<h1>Termos de Utilização</h1><p>Regras e diretrizes.</p>",
    };

    appContent.innerHTML = pages[page] || "<h1>Página não encontrada</h1>";

    // Após carregar a página, adicionar o event listener ao formulário
    setTimeout(() => {
        const authForm = document.getElementById(`${page}-form`);
        if (authForm) {
            authForm.addEventListener("submit", (event) => handleAuthFormSubmit(event, page));
        }
    }, 100);
}

/**
 * Página dinâmica de Login e Registo
 */
function loadAuthPage(type) {
    return `
        <section class="auth-container">
            <h2>${type === "login" ? "Iniciar Sessão" : "Criar Conta"}</h2>
            <form id="${type}-form">
                ${type === "register" ? `<input type="text" id="name" placeholder="Nome Completo" required>` : ""}
                <input type="email" id="email" placeholder="E-mail" required>
                <input type="password" id="password" placeholder="Palavra-passe" required>
                <button type="submit" class="btn">${type === "login" ? "Entrar" : "Registar"}</button>
            </form>
            <p>${type === "login" ? "Ainda não tem conta?" : "Já tem conta?"}  
                <a href="#" onclick="loadPage('${type === 'login' ? 'register' : 'login'}')">
                    ${type === "login" ? "Registe-se aqui" : "Inicie sessão"}
                </a>
            </p>
        </section>
    `;
}

/**
 * Enviar dados de Login ou Registo para o backend
 */
async function handleAuthFormSubmit(event, type) {
    event.preventDefault(); // Previne recarregamento da página

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const url = type === "register" ? "http://localhost:3000/api/users/register" : "http://localhost:3000/api/users/login";

    let payload = { user_email: email, user_password: password };

    // Se for registo, adicionar nome e tipo de utilizador (pode ser fixo para 1 por enquanto)
    if (type === "register") {
        const name = document.getElementById("name").value;
        payload.user_name = name;
        payload.type_id = 1; // Tipo de utilizador padrão
    }

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
        });

        const data = await response.json();
        alert(data.message || "Operação realizada com sucesso!");

        if (response.ok && type === "register") {
            loadPage("login"); // Redireciona para login após registo
        }
    } catch (error) {
        alert("Erro ao processar a solicitação. Tente novamente mais tarde.");
        console.error(error);
    }
}