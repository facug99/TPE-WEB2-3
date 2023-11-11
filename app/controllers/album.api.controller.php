<?php

require_once 'app/controllers/api.controller.php';
require_once 'app/models/album.model.php';

class AlbumAPIController extends APIController {
    
    public function __construct() {
        parent::__construct();
        $this->model = new AlbumModel();
    }

    /**
     * Crea un álbum con los atributos pasados por JSON
     */
    public function create() {
        $body = $this->getData();

        $title = $body->title;
        $year = $body->year;
        $bandId = $body->band_id;

        // TO-DO: Verificar si existe la banda

        $id = $this->model->insertAlbum($title, $year, $bandId);
        
        if ($id) {
            $this->view->response("Album id=$id successfully created", 201);
        } else {
            $this->view->response("Album id=$id could not be created", 422);
        }
    }

    /**
     * Devuelve un JSON con el o los álbumes, dependiendo si se recibe o no el parámetro ":id"
     */
    public function getAll($params = []) {
        $filterField = $value = $sortField = $order = "";

        /* TODO: Filtro de búsqueda por campo y valor dados
        $fields = ['id', 'title', 'year', 'band_id'];
        if (!empty($_GET['title'])) 
            $filterField = $_GET['title'];
        */

        // Ordenamiento por un campo dado
        if (!empty($_GET['sort'])) {
            $sortField = $_GET['sort'];

            // Orden ascendente o descendente
            if (!empty($_GET['order']))
                $order = $_GET['order'];
        }

        /* TODO: paginación
        if (!empty($_GET['page'] && !empty($_GET['page_size']))) {
            $page = $_GET['page'];
            $page_size = $_GET['page_size'];
        }
        */

        $albums = $this->model->getAlbums($filterField, $value, $sortField, $order);
        return $this->view->response($albums, 200);
    }

    /**
     * Devuelve un JSON de un álbum con ID específico
     */
    public function get($params = []) {
        $id = $params[':id'];
        $album = $this->model->getAlbumById($id);
        if (!empty($album))
            return $this->view->response($album, 200);
        else 
            return $this->view->response("Album id=$id not found", 404);
    }

    /**
     * Actualiza un álbum dado
     */
    public function update($params = []) {
        if (empty($params)) {
            $this->view->response("Album not specified", 400);
            return;
        }

        $id = $params[':id'];
        $album = $this->model->getAlbumById($id);

        if ($album) {
            $body = $this->getData();
            $title = $body->title;
            $year = $body->year;
            $bandId = $body->band_id;
            
            // TO-DO verificar si pudo modificarse el álbum en la DB

            $this->model->editAlbum($id, $title, $year, $bandId);
            $this->view->response("Album id=$id successfully modified", 200);
        } else 
            $this->view->response("Album id=$id not found", 404);
    }

    /**
     * Elimina un álbum según un ID pasado por parámetro
     */
    public function delete($params = []) {
        if (empty($params)) {
            $this->view->response("Album not specified", 400);
            return;
        }

        $id = $params[':id'];
        $album = $this->model->getAlbumById($id);

        if ($album) {
            $this->model->deleteAlbum($id);
            $this->view->response("Album id=$id deleted", 200);            
        } else
            $this->view->response("Album id=$id not found", 404);
    }
}