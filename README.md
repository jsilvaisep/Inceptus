## 🚀 Inceptus - Plataforma de Avaliação de Produtos e Empresas

Inceptus é um projeto académico, elaborado na disciplina de Laboratório de Projeto II, que permite aos utilizadores pesquisar, avaliar e comparar produtos e empresas, inspirado no **Product Hunt**. 
O projeto inclui um **frontend (HTML, CSS, JavaScript)** e um **backend (Node.js + Express + MySQL)**.

---

## ⚙️ **Pré-requisitos**
Antes de executar o projeto, certifica-te de que tens os seguintes programas instalados:

🔹 [**Node.js** (versão 18 ou superior)](https://nodejs.org/)  
🔹 [**MySQL Server**](https://dev.mysql.com/downloads/)  
🔹 [**Git**](https://git-scm.com/downloads)  
🔹 [**Visual Studio Code** (ou WebStorm)](https://code.visualstudio.com/)  
🔹 (Opcional) [**Docker**](https://www.docker.com/) para rodar MySQL localmente

---

## 📂 **Estrutura do Projeto**
O projeto está organizado da seguinte forma:
```
Inceptus/
├── backend/                    # Backend (Node.js + Express + MySQL)
│   ├── config/                 # Configuração da ligação à base de dados
│   │   ├── database.js
│   ├── models/                 # Modelos da base de dados (Sequelize)
│   │   ├── Category.js
│   │   ├── Comment.js
│   │   ├── Company.js
│   │   ├── index.js            # Inicializa Sequelize e importa modelos
│   │   ├── Product.js         
│   │   ├── User.js
│   ├── node_modules/           # Módulos instalados pelo npm (ignorado no Git)
│   ├── routes/                 # Rotas da API (endpoints do backend)
│   │   ├── categoryRoutes.js
│   │   ├── clienteRoutes.js
│   │   ├── commentRoutes.js
│   │   ├── companyRoutes.js
│   │   ├── productRoutes.js
│   ├── .env                    # Configuração de ambiente (ignorado no Git)
│   ├── package.json            # Dependências do backend
│   ├── package-lock.json       # Registo das versões das dependências
│   └── server.js               # Configuração principal do servidor
│
├── frontend/                   # Frontend (HTML, CSS, JavaScript)
│   ├── img/                    # Imagens e outros ficheiros estáticos
│   ├── index.html              # Página principal
│   ├── styles.css              # Estilos CSS
│   └── script.js               # Lógica do frontend
├── .gitignore                  # Ficheiros ignorados no Git
└── README.md                   # Documentação do projeto
```

---
## 🛠 **Tecnologias Utilizadas**
O projeto usa as seguintes tecnologias:

### 🔹 **Frontend**
- HTML5 + CSS3 + JavaScript puro
- **SPA** (Single Page Application) sem frameworks
- Design Responsivo

### 🔹 **Backend**
- **Node.js** + **Express.js**
- **MySQL** como base de dados (via **Sequelize ORM**)
- Autenticação via **JWT (JSON Web Token)**

### 🔹 **Infraestrutura**
- Configuração de variáveis ambiente via `.env`
- **Docker** (opcional para ambiente isolado)