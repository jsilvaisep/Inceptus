const express = require("express");
const Cliente = require("../models/cliente");

const router = express.Router();

// Criar Cliente
router.post("/", async (req, res) => {
    try {
        const cliente = await Cliente.create(req.body);
        res.status(201).json(cliente);
    } catch (err) {
        res.status(500).json({ error: "Erro ao criar cliente" });
    }
});

// Listar Clientes
router.get("/", async (req, res) => {
    const clientes = await Cliente.findAll();
    res.json(clientes);
});

// Obter Cliente por ID
router.get("/:id", async (req, res) => {
    const cliente = await Cliente.findByPk(req.params.id);
    cliente ? res.json(cliente) : res.status(404).json({ error: "Cliente não encontrado" });
});

// Atualizar Cliente
router.put("/:id", async (req, res) => {
    const cliente = await Cliente.update(req.body, { where: { id_cliente: req.params.id } });
    cliente[0] ? res.json({ message: "Cliente atualizado" }) : res.status(404).json({ error: "Cliente não encontrado" });
});

// Apagar Cliente
router.delete("/:id", async (req, res) => {
    const apagado = await Cliente.destroy({ where: { id_cliente: req.params.id } });
    apagado ? res.json({ message: "Cliente removido" }) : res.status(404).json({ error: "Cliente não encontrado" });
});

module.exports = router;