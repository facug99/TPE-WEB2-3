<?php

require_once 'app/views/api.view.php';

abstract class APIController {
    protected $model;
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
}
