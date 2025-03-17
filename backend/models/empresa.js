const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Empresa = sequelize.define("Empresa", {
    id_empresa: { type: DataTypes.STRING(15), primaryKey: true },
    nome_empresa: { type: DataTypes.STRING(100), allowNull: false },
    email_empresa: { type: DataTypes.STRING(100), allowNull: false, unique: true },
    password: { type: DataTypes.STRING(200), allowNull: false },
    site: { type: DataTypes.STRING(100) },
    status: { type: DataTypes.STRING(1), allowNull: false },
    data_criacao: { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
    classificacao: { type: DataTypes.INTEGER }
}, { timestamps: false });

module.exports = Empresa;