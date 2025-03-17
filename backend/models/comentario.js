const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");
const Cliente = require("./Cliente");
const Empresa = require("./Empresa");
const Produto = require("./Produto");

const Comentario = sequelize.define("Comentario", {
    id_comentario: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    comentario: { type: DataTypes.TEXT, allowNull: false },
    status: { type: DataTypes.STRING(1), allowNull: false },
    data_criacao: { type: DataTypes.DATE, defaultValue: DataTypes.NOW }
}, { timestamps: false });

// Relacões
Cliente.hasMany(Comentario, { foreignKey: "id_cliente" });
Comentario.belongsTo(Cliente, { foreignKey: "id_cliente" });

Empresa.hasMany(Comentario, { foreignKey: "id_empresa" });
Comentario.belongsTo(Empresa, { foreignKey: "id_empresa" });

Produto.hasMany(Comentario, { foreignKey: "id_produto" });
Comentario.belongsTo(Produto, { foreignKey: "id_produto" });

module.exports = Comentario;