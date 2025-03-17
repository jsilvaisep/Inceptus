## 🚀 Inceptus - Plataforma de Avaliação de Produtos e Empresas

Inceptus é um projeto académico, elaborado na disciplina de Laboratório de Projeto II, que permite aos utilizadores pesquisar, avaliar e comparar produtos e empresas, inspirado no **Product Hunt**. 
O projeto inclui um **frontend (HTML, CSS, JavaScript)** e um **backend (Node.js + Express + MySQL)**.

---

## 📂 **Estrutura do Projeto**
O projeto está organizado da seguinte forma:
```
inceptus/
├── backend/                # Backend (Node.js + Express + MySQL)
│   ├── models/             # Modelos da base de dados (Sequelize)
│   ├── routes/             # Rotas da API (endpoints do backend)
│   ├── config/             # Configuração da ligação à base de dados
│   ├── server.js           # Configuração principal do servidor
│   ├── .env                # Configuração de ambiente (ignorado no Git)
│   ├── package.json        # Dependências do backend
│   ├── package-lock.json   # Registo das versões das dependências
│   ├── node_modules/       # Módulos instalados pelo npm (ignorado no Git)
│
├── frontend/               # Frontend (HTML, CSS, JavaScript)
│   ├── index.html          # Página principal
│   ├── styles.css          # Estilos CSS
│   ├── script.js           # Lógica do frontend
│
├── img/                    # Imagens e outros ficheiros estáticos
├── README.md               # Documentação do projeto
└── .gitignore              # Ficheiros ignorados no Git
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
