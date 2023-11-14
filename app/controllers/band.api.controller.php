<?php

require_once 'app/controllers/api.controller.php';
require_once 'app/models/band.model.php';

class BandAPIController extends APIController {
    private $bandModel;

    public function __construct() {
        parent::__construct();
        $this->bandModel = new BandModel();
    }

    /**
     * Devuelve un arreglo con las bandas de la base de datos
     */
    public function getAll() {
        // Se obtienen nombres de columnas de la tabla para futuras verificaciones
        $columns = $this->bandModel->getColumnNames();

        // Arreglo donde se almacenarán los parámetros de consulta
        $queryParams = array();

        // Filtro
        $queryParams += $this->handleFilter($columns);

        // Ordenamiento
        $queryParams += $this->handleSort($columns);

        // Paginación
        $queryParams += $this->handlePagination();

        // Generación de sentencia SQL a partir de parámetros de consulta
        $sql = $this->buildSqlQuery("bands", $queryParams);

        // Se obtienen los álbumes y se devuelven en formato JSON
        $bands = $this->bandModel->getBands($sql, $queryParams['value']);
        return $this->view->response($bands, 200);
    }

    /**
     * Devuelve el JSON de una banda con ID específico
     */
    public function get($params = []) {
        $id = $params[':id'];
        $band = $this->bandModel->getBandById($id);
 
        if (!empty($band))
            return $this->view->response($band, 200);
        else
            return $this->view->response("Band id=$id not found", 404);
    }
}