const sequelize = require("../config/database");

const Cliente = require("./Cliente");
const Empresa = require("./Empresa");
const Categoria = require("./Categoria");
const Produto = require("./Produto");
const Comentario = require("./Comentario");

// Sincronizar modelos
sequelize.sync({ alter: true }) // Usa alter: true para atualizar a estrutura sem perder dados
    .then(() => console.log("🟢 Modelos sincronizados com a BD"))
    .catch((err) => console.error("🔴 Erro ao sincronizar modelos:", err));

module.exports = {
    Cliente,
    Empresa,
    Categoria,
    Produto,
    Comentario,
};