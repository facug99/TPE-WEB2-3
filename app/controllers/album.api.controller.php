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
        
        // Arreglo donde se almacenarán los parámetros de consulta
        $queryParams = array();

        // Filtro
        $queryParams += $this->handleFilter($columns);

        // Ordenamiento
        $queryParams += $this->handleSort($columns);

        // Paginación
        $queryParams += $this->handlePagination();

        // Se obtienen los álbumes y se devuelven en formato JSON
        $albums = $this->albumModel->getAlbums($queryParams);
        return $this->view->response($albums, 200);
    }

    /**
     * Devuelve el JSON de un álbum con ID específico
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
     * Se modifica un álbum dado su ID
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
     * Elimina un álbum dado su ID
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

    /**
     * Método de filtrado de resultados según campo y valor dados
     */
    private function handleFilter($columns) {
        // Valores por defecto
        $filterData = [
            'filter' => "", // Campo de filtrado
            'value' => ""   // Valor de filtrado
        ];
    
        if (!empty($_GET['filter']) && !empty($_GET['value'])) {
            $filter = strtolower($_GET['filter']);
            $value = strtolower($_GET['value']);
            
            // Si el campo no existe se produce un error
            if (!in_array($filter, $columns)) {
                $this->view->response("Invalid filter parameter (field '$filter' does not exist)", 400);
                die();
            }

            $filterData['filter'] = $filter;
            $filterData['value'] = $value;
        }
    
        return $filterData;
    }

    /**
     * Método de ordenamiento de resultados según campo y orden dados
     */
    private function handleSort($columns) {
        // Valores por defecto
        $sortData = [
            'sort' => "", // Campo de ordenamiento
            'order' => "" // Orden ascendente o descendente
        ];
    
        if (!empty($_GET['sort'])) {
            $sort = strtolower($_GET['sort']);
    
            // Si el campo de ordenamiento no existe se produce un error
            if (!in_array($sort, $columns)) {
                $this->view->response("Invalid sort parameter (field '$sort' does not exist)", 400);
                die();
            }    
    
            // Orden ascendente o descendente
            if (!empty($_GET['order'])) {
                $order = strtoupper($_GET['order']);
                $allowedOrders = ['ASC', 'DESC'];
    
                // Si el campo de ordenamiento no existe se produce un error
                if (!in_array($order, $allowedOrders)) {
                    $this->view->response("Invalid order parameter (only 'ASC' or 'DESC' allowed)", 400);
                    die();
                }
            }
    
            $sortData['sort'] = $sort;
            $sortData['order'] = $order;
        }
    
        return $sortData;
    }

    /**
     * Método de paginación de resultados según número de página y límite dados
     */
    private function handlePagination() {
        // Valores por defecto
        $paginationData = [
            'limit' => 0,    // Límite de resultados
            'offset' => 0    // Desplazamiento
        ];
    
        if (!empty($_GET['page']) && !empty($_GET['limit'])) {
            $page = $_GET['page'];
            $limit = $_GET['limit'];
    
            // Si alguno de los valores no es un número natural se produce un error
            if (!is_numeric($page) || $page < 0 || !is_numeric($limit) || $limit < 0) {
                $this->view->response("Page and limit parameters must be positive integers", 400);
                die();
            }
            
            $offset = ($page - 1) * $limit;
    
            $paginationData['limit'] = $limit;
            $paginationData['offset'] = $offset;
        }
    
        return $paginationData;
    }
}