<?php

require_once 'app/views/api.view.php';

abstract class APIController {
    protected $view;
    private $data;

    public function __construct() {
        $this->view = new APIView();
        $this->data = file_get_contents('php://input');
    }

    /**
     * Decodifica un JSON y lo convierte en un objeto
     */
    public function getData() {
        return json_decode($this->data);
    }

    /**
     * Método de filtrado de resultados según campo y valor dados
     */
    protected function handleFilter($columns) {
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
    protected function handleSort($columns) {
        // Valores por defecto
        $sortData = [
            'sort' => "", // Campo de ordenamiento
            'order' => "" // Orden ascendente o descendente
        ];

        if (!empty($_GET['sort'])) {
            $sort = strtolower($_GET['sort']);

            // Si el campo de ordenamiento no existe en la tabla se produce un error
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
    protected function handlePagination() {
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

    /**
     * Construye una sentencia SQL con parámetros de consulta dados
     */
    protected function buildSqlQuery($table, $queryParams) {
        $sql = "SELECT * FROM $table";

        // Filtro
        if (!empty($queryParams['filter']) && !empty($queryParams['value']))
            $sql .= ' WHERE ' . $queryParams['filter'] . ' LIKE :value';

        // Ordenamiento
        if (!empty($queryParams['sort'])) {
            $sql .= ' ORDER BY ' . $queryParams['sort'];

            // Orden ascendente y descendente
            if (!empty($queryParams['order']))
                $sql .= ' ' . $queryParams['order'];
        }

        // Paginación
        if (!empty($queryParams['limit']))
            $sql .= ' LIMIT ' . $queryParams['limit'] . ' OFFSET ' . $queryParams['offset'];

        return $sql;
    }
}
