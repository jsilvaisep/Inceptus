const { Sequelize } = require("sequelize");
require("dotenv").config();

const sequelize = new Sequelize(
    process.env.DB_NAME,
    process.env.DB_USER,
    process.env.DB_PASS,
    {
        host: process.env.DB_HOST,
        dialect: "mysql",
        logging: false, // Define para true se quiseres ver os logs SQL no terminal
        define: {
            freezeTableName: true,   // Evita pluralização automática
            underscored: true,       // Converte camelCase para snake_case
        }
    }
);

// Testar a conexão
sequelize
    .authenticate()
    .then(() => console.log("🟢 Ligação à BD com sucesso"))
    .catch((err) => console.error("🔴 Erro ao ligar à BD:", err));

module.exports = sequelize;