<?php

// Nombre de la aplicación
const APP_NAME = "LocalSoundBA";

// URL base para utilizar URLs semánticas
define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

// Información de la base de datos
const DB_HOST = 'localhost';
const DB_NAME = 'web2_tpe';
const DB_CHARSET = 'utf8';
const DB_USER = 'root';
const DB_PASS = '';

// Información de JWT
const JWT_KEY = 'webadmin2023';
const JWT_EXP = 3600;
