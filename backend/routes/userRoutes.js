const express = require("express");
const bcrypt = require("bcrypt");
const User = require("../models/User");const router = express.Router();
const jwt = require("jsonwebtoken");

// Rota para registar um novo utilizador
router.post("/register", async (req, res) => {
    try {
        const { user_name, user_email, user_password } = req.body;

        // Verifica se o email já existe
        const existingUser = await User.findOne({ where: { user_email } });
        if (existingUser) {
            return res.status(400).json({ message: "Email já registado" });
        }

        // Hash da senha
        const hashedPassword = await bcrypt.hash(user_password, 10);

        // Criação do usuário
        const newUser = await User.create({
            user_name,
            user_email,
            user_password: hashedPassword
        });

        res.status(201).json({ message: "Utilizador registado com sucesso", user: newUser });
    } catch (error) {
        res.status(500).json({ message: "Erro ao registar utilizador", error: error.message });
    }
});

module.exports = router;


// Rota para login do utilizador
router.post("/login", async (req, res) => {
    try {
        const { user_email, user_password } = req.body;

        // Verifica se o utilizador existe
        const user = await User.findOne({ where: { user_email } });
        if (!user) {
            return res.status(400).json({ message: "Email ou senha inválidos" });
        }

        // Compara a senha
        const passwordMatch = await bcrypt.compare(user_password, user.user_password);
        if (!passwordMatch) {
            return res.status(400).json({ message: "Email ou senha inválidos" });
        }

        // Gera um token JWT
        const token = jwt.sign({ userId: user.user_id, email: user.user_email }, "segredo123", { expiresIn: "1h" });

        res.status(200).json({ message: "Login bem-sucedido", token });
    } catch (error) {
        res.status(500).json({ message: "Erro ao fazer login", error: error.message });
    }
});