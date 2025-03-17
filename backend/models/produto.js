const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");
const Empresa = require("./Empresa");
const Categoria = require("./Categoria");

const Produto = sequelize.define("Produto", {
    id_produto: { type: DataTypes.STRING(15), primaryKey: true },
    nome_produto: { type: DataTypes.STRING(100), allowNull: false },
    status: { type: DataTypes.STRING(1), allowNull: false },
    classificacao: { type: DataTypes.INTEGER }
}, { timestamps: false });

// Relações
Empresa.hasMany(Produto, { foreignKey: "id_empresa" });
Produto.belongsTo(Empresa, { foreignKey: "id_empresa" });

Categoria.hasMany(Produto, { foreignKey: "id_categoria" });
Produto.belongsTo(Categoria, { foreignKey: "id_categoria" });

module.exports = Produto;