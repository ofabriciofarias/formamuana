<?php

/**
 * Configurações para envio de email via SMTP.
 * 
 * Preencha com as credenciais do seu servidor de email.
 * Para usar o Gmail, é necessário gerar uma "Senha de App".
 */

// Servidor SMTP (ex: 'smtp.gmail.com' para Gmail)
define('MAIL_HOST', 'smtp.gmail.com');

// Habilitar autenticação SMTP
define('MAIL_SMTP_AUTH', true);

// Usuário do SMTP (seu email completo)
define('MAIL_USERNAME', 'fabriciosouzafarias@gmail.com');

// Senha do SMTP (sua senha de email ou senha de app)
define('MAIL_PASSWORD', 'uasp cwsk acpd odty');

// Tipo de criptografia: 'ssl' (porta 465) ou 'tls' (porta 587)
define('MAIL_SMTP_SECURE', 'ssl');

// Porta do servidor SMTP
define('MAIL_PORT', 465);

// Nome e email do remetente que aparecerão no email
define('MAIL_FROM_NAME', 'Forma Muana');
define('MAIL_FROM_EMAIL', 'fabriciosouzafarias@gmail.com');