a# 🚀 Inceptus - Plataforma de Avaliação de Produtos e Empresas

Inceptus é um projeto académico, elaborado na disciplina de Laboratório de Projeto II, que permite aos utilizadores pesquisar, avaliar e comparar produtos e empresas, inspirado no **Product Hunt**. 
O projeto inclui um **frontend (HTML, CSS, JavaScript)** e um **backend (Node.js + Express + MySQL)**.

---

## 📂 **Estrutura do Projeto**
O projeto está organizado da seguinte forma:

backend/                 # Backend em Node.js + Express + MySQL
  models/                # Modelos do banco de dados (Sequelize)  
  routes/                # Rotas da API (endpoints do backend)
  config/                # Configuração do banco de dados
  server.js              # Arquivo principal do servidor
  .env                   # Configuração de ambiente (NÃO enviar para o Git!)
  package.json           # Dependências do backend
  package-lock.json      # Registo das versões das dependências
  node_modules/          # Módulos instalados pelo npm (IGNORADO no Git)
frontend/                # Frontend em HTML, CSS, JavaScript puro
  index.html             # Página principal
  styles.css             # Estilos CSS
  script.js              # Lógica do frontend
img/                     # Imagens e outros ficheiros estáticos
README.md                # Documentação do projeto
.gitignore               # Ficheiros ignorados no Git

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
