const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const User = sequelize.define("User", {
    user_id: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    user_name: {
        type: DataTypes.STRING,
        allowNull: false
    },
    user_email: {
        type: DataTypes.STRING,
        allowNull: false,
        unique: true
    },
    user_password: {
        type: DataTypes.STRING,
        allowNull: false
    },
    user_status: {
        type: DataTypes.STRING,
        allowNull: false,
        defaultValue: "A"
},
    type_id: {
        type: DataTypes.INTEGER,
        allowNull: false,
        defaultValue: 1
    },
    img_url: {
        type: DataTypes.STRING
    },
}, {
    tableName: "USERS", // 🔹 Define o nome exato da tabela
    timestamps: true
});

module.exports = User;