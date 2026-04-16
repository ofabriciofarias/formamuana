<?php

/**
 * Configurações para envio de email via SMTP.
 * 
 * Preencha com as credenciais do seu servidor de email.
 * Para usar o Gmail, é necessário gerar uma "Senha de App".
 */

define('MAIL_HOST',        getenv('MAIL_HOST')        ?: 'smtp.gmail.com');
define('MAIL_SMTP_AUTH',   true);
define('MAIL_USERNAME',    getenv('MAIL_USERNAME')    ?: '');
define('MAIL_PASSWORD',    getenv('MAIL_PASSWORD')    ?: '');
define('MAIL_SMTP_SECURE', getenv('MAIL_SMTP_SECURE') ?: 'ssl');
define('MAIL_PORT',        getenv('MAIL_PORT')        ?: 465);
define('MAIL_FROM_NAME',   getenv('MAIL_FROM_NAME')   ?: 'Forma Muana');
define('MAIL_FROM_EMAIL',  getenv('MAIL_FROM_EMAIL')  ?: '');