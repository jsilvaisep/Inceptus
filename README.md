## 🚀 Inceptus - Plataforma de Avaliação de Produtos e Empresas

Inceptus é um projeto académico, elaborado na disciplina de Laboratório de Projeto II, que permite aos utilizadores pesquisar, avaliar e comparar produtos e empresas, inspirado no **Product Hunt**. 
O projeto inclui um **frontend (HTML, CSS, JavaScript)** e um **backend (Node.js + Express + MySQL)**.

---

## 📂 **Estrutura do Projeto**
O projeto está organizado da seguinte forma:
```
Inceptus/
├── backend/                    # Backend (Node.js + Express + MySQL)
│   ├── config/                 # Configuração da ligação à base de dados
│   │   ├── database.js
│   ├── models/                 # Modelos da base de dados (Sequelize)
│   │   ├── categoria.js
│   │   ├── cliente.js
│   │   ├── comentario.js
│   │   ├── empresa.js
│   │   ├── index.js            # Inicializa Sequelize e importa modelos
│   │   ├── produto.js         
│   ├── node_modules/           # Módulos instalados pelo npm (ignorado no Git)
│   ├── routes/                 # Rotas da API (endpoints do backend)
│   │   ├── categoriaRoutes.js
│   │   ├── clienteRoutes.js
│   │   ├── comentarioRoutes.js
│   │   ├── empresaRoutes.js
│   │   ├── produtoRoutes.js
│   ├── .env                    # Configuração de ambiente (ignorado no Git)
│   ├── package.json            # Dependências do backend
│   ├── package-lock.json       # Registo das versões das dependências
│   └── server.js               # Configuração principal do servidor
│
├── frontend/                   # Frontend (HTML, CSS, JavaScript)
│   ├── index.html              # Página principal
│   ├── styles.css              # Estilos CSS
│   ├── script.js               # Lógica do frontend
│
├── img/                        # Imagens e outros ficheiros estáticos
├── .gitignore                  # Ficheiros ignorados no Git
└── README.md                   # Documentação do projeto
```
---

## 🛠 **Tecnologias Utilizadas**
O projeto usa as seguintes tecnologias:

### 🔹 **Frontend**
- HTML, CSS, JavaScript

### 🔹 **Backend**
- Node.js + Express.js
- Base de Dados **MySQL** via Sequelize
- Autenticação com **JWT** 

### 🔹 **Infraestrutura**
- Variáveis de ambiente via `.env`

---
