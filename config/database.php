<?php

// Configurações do banco de dados
// Em Docker, os valores vêm das variáveis de ambiente definidas no docker-compose.yml
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'forma_muana');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
