const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");
const Empresa = require("./Empresa");

const Categoria = sequelize.define("Categoria", {
    id_categoria: { type: DataTypes.STRING(15), primaryKey: true },
    nome_categoria: { type: DataTypes.STRING(100), allowNull: false },
    tipo: { type: DataTypes.STRING(100) }
}, { timestamps: false });

// Relações
Empresa.hasMany(Categoria, { foreignKey: "id_empresa" });
Categoria.belongsTo(Empresa, { foreignKey: "id_empresa" });

module.exports = Categoria;