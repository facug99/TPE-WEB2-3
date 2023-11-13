<?php

class APIView {
    /**
     * Establece header con el resultado de la operación y 
     * codifica la información en formato JSON
     */
    public function response($data, $status = 200) {
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . $status . ' ' . $this->_requestStatus($status));
        echo json_encode($data);
    }

    /**
     * Devuelve un mensaje asociado al código de respuesta dado
     */
    private function _requestStatus($code) {
        $status = array(
            200 => 'OK',
            201 => 'Created',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            422 => 'Unprocessable Entity',
            404 => 'Not found',
            500 => 'Internal Server Error'
        );

        return isset($status[$code]) ? $status[$code] : $status[500];
    }
}
