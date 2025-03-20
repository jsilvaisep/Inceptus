const express = require("express");
const cors = require("cors");
const bodyParser = require("body-parser");
const db = require("./config/database");
const userRoutes = require("./routes/userRoutes");

// 🔹 Importa os modelos para garantir que são carregados e sincronizados
require("./models");

const app = express();
app.use(cors());
app.use(bodyParser.json());
app.use("/api/users", userRoutes);

// Testa a conexão com a BD
db.authenticate()
    .then(() => console.log("✅ Conectado ao MySQL"))
    .catch(err => console.error("❌ Erro na conexão:", err));

const PORT = 3000;
app.listen(PORT, () => console.log(`🚀 Servidor a correr em http://localhost:${PORT}`));