<?php

require_once 'app/controllers/api.controller.php';
require_once 'app/models/album.model.php';

class AlbumAPIController extends APIController {
    private $albumModel;
    private $bandModel;

    public function __construct() {
        parent::__construct();
        $this->albumModel = new AlbumModel();
        // $this->bandModel = new BandModel();
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

        $id = $this->albumModel->insertAlbum($title, $year, $bandId);
        
        if ($id) {
            $this->view->response("Album id=$id successfully created", 201);
        } else {
            $this->view->response("Album id=$id could not be created", 422);
        }
    }

    /**
     * Devuelve un JSON con los álbumes de la base de datos
     */
    public function getAll() {
        // Se obtienen nombres de columnas de la tabla para futuras verificaciones
        $columns = $this->albumModel->getColumnNames();
        
        // Filtro
        $filter = $value = ""; // Valores por defecto

        if (!empty($_GET['filter']) && !empty($_GET['value'])) {
            $filter = strtolower($_GET['filter']);
            $value = strtolower($_GET['value']);
            
            // Si el campo no existe se informa el error
            if (!in_array($filter, $columns)) {
                $this->view->response("Invalid filter parameter (field '$filter' does not exist)", 400);
                return;
            }
        }

        // Ordenamiento por un campo dado
        $sort = $order = ""; // Valores por defecto

        if (!empty($_GET['sort'])) {
            $sort = strtolower($_GET['sort']);

            // Si el campo de ordenamiento no existe se informa el error
            if (!in_array($sort, $columns)) {
                $this->view->response("Invalid sort parameter (field '$sort' does not exist)", 400);
                return;
            }    

            // Orden ascendente o descendente
            if (!empty($_GET['order'])) {
                $order = strtoupper($_GET['order']);
                $allowedOrders = ['ASC', 'DESC'];

                // Si el campo de ordenamiento no existe se informa el error
                if (!in_array($order, $allowedOrders)) {
                    $this->view->response("Invalid order parameter (only 'ASC' or 'DESC' allowed)", 400);
                    return;
                }
            }
        }

        // Paginación
        $page = $limit = $offset = 0; // Valores por defecto

        if (!empty($_GET['page']) && !empty($_GET['limit'])) {
            $page = $_GET['page'];
            $limit = $_GET['limit'];

            if (!is_numeric($page) || !is_numeric($limit)) {
                $this->view->response("Page and limit parameters must be numeric", 400);
                return;
            }
            
            $offset = ($page - 1) * $limit;
        }

        // Se obtienen los álbumes y se devuelven
        $albums = $this->albumModel->getAlbums($filter, $value, $sort, $order, $limit, $offset);
        return $this->view->response($albums, 200);
    }

    /**
     * Devuelve un JSON de un álbum con ID específico
     */
    public function get($params = []) {
        $id = $params[':id'];
        $album = $this->albumModel->getAlbumById($id);
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
        $album = $this->albumModel->getAlbumById($id);

        if ($album) {
            $body = $this->getData();
            $title = $body->title;
            $year = $body->year;
            $bandId = $body->band_id;
            
            // TO-DO verificar si pudo modificarse el álbum en la DB

            $this->albumModel->editAlbum($id, $title, $year, $bandId);
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
        $album = $this->albumModel->getAlbumById($id);

        if ($album) {
            $this->albumModel->deleteAlbum($id);
            $this->view->response("Album id=$id deleted", 200);            
        } else
            $this->view->response("Album id=$id not found", 404);
    }
}