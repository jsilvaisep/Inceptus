const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Company = sequelize.define("Company", {
  company_id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
  company_name: { type: DataTypes.STRING(100), allowNull: false },
  company_description: { type: DataTypes.TEXT },
  company_email: { type: DataTypes.STRING(100), allowNull: false, unique: true },
  company_password: { type: DataTypes.STRING(255), allowNull: false },
  company_site: { type: DataTypes.STRING(255) },
  company_status: { type: DataTypes.STRING(1), defaultValue: "A" },
  company_rank: { type: DataTypes.INTEGER },
  img_url: { type: DataTypes.STRING(255) },
  created_at: { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
  updated_at: { type: DataTypes.DATE, defaultValue: DataTypes.NOW }
}, { timestamps: false });

module.exports = Company;