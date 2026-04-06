<?php
require 'config/database.php';
require 'config/session.php';
require 'core/Database.php';
require 'core/helpers.php';
require 'models/Oferta.php';
require 'models/Aluno.php';
require 'models/AlunoOferta.php';
require 'controllers/ProfessorController.php';

$_SESSION['user_id'] = 311;
$_SESSION['user_role_id'] = 2;
$c = new ProfessorController();
$c->dashboard();