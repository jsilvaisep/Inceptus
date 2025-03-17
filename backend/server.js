const express = require("express");
const cors = require("cors");
const bodyParser = require("body-parser");
require("dotenv").config();
const sequelize = require("./config/database");

// Importar modelos para garantir sincronização
require("./models");

const clienteRoutes = require("./routes/clienteRoutes");
//const empresaRoutes = require("./routes/empresaRoutes");
//const categoriaRoutes = require("./routes/categoriaRoutes");
//const produtoRoutes = require("./routes/produtoRoutes");
//const comentarioRoutes = require("./routes/comentarioRoutes");

const app = express();
app.use(cors());
app.use(bodyParser.json());

app.use("/api/clientes", clienteRoutes);
//app.use("/api/empresas", empresaRoutes);
//app.use("/api/categorias", categoriaRoutes);
//app.use("/api/produtos", produtoRoutes);
//app.use("/api/comentarios", comentarioRoutes);

// Testar e sincronizar a base de dados antes de iniciar o servidor
sequelize.sync().then(() => {
    console.log("🟢 Base de dados em cima");
    app.listen(3000, () => console.log("🚀 Servidor rodando na porta 3000"));
});