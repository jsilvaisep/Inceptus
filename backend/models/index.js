const sequelize = require("../config/database");

const Categoria = require("./Category");
const Comment = require("./Comment");
const Empresa = require("./Company");
const Produto = require("./Product");
const Cliente = require("./User");

// Sincronizar modelos
sequelize.sync()
    .then(() => console.log("🟢 Modelos sincronizados com a BD"))
    .catch((err) => console.error("🔴 Erro ao sincronizar modelos:", err));

module.exports = {
    Cliente,
    Empresa,
    Categoria,
    Produto,
    Comment
};