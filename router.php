<?php

require_once 'libs/router.php'; // Se importa el router avanzado
require_once 'config.php';
require_once 'app/controllers/album.api.controller.php';

// Se instancia el router
$router = new Router();

// Se define la tabla de routing
// $router->addRoute(recurso, verboHTTP, controlador, método);
$router->addRoute('albums', 'GET', 'AlbumApiController', 'getAll');
$router->addRoute('albums', 'POST', 'AlbumApiController', 'create');
$router->addRoute('albums', 'PUT', 'AlbumApiController','update');
$router->addRoute('albums', 'DELETE', 'AlbumApiController', 'delete');
$router->addRoute('albums/:id', 'GET', 'AlbumApiController', 'get');
$router->addRoute('albums/:id', 'PUT', 'AlbumApiController', 'update');
$router->addRoute('albums/:id', 'DELETE', 'AlbumApiController', 'delete');


// Se rutea
$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);