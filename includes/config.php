<?php
/**
 * Carrega variáveis de ambiente do arquivo .env
 */
function loadEnv() {
    $envFile = dirname(__DIR__) . '/.env';

    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Processa linha com variável
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove aspas se existirem
            if (preg_match('/^"(.+)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.+)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

// Carrega variáveis de ambiente
loadEnv();
?>