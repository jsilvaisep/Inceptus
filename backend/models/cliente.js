const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Cliente = sequelize.define("Cliente", {
    id_cliente: { type: DataTypes.STRING(15), primaryKey: true },
    nome_cliente: { type: DataTypes.STRING(100), allowNull: false },
    email_cliente: { type: DataTypes.STRING(100), allowNull: false, unique: true },
    password: { type: DataTypes.STRING(200), allowNull: false },
    status: { type: DataTypes.STRING(1), allowNull: false },
    data_criacao: { type: DataTypes.DATE, defaultValue: DataTypes.NOW }
}, { timestamps: false });

module.exports = Cliente;