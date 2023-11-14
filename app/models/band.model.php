<?php

require_once "app/models/model.php";

class BandModel extends Model {
    /**
     * Devuelve el nombre de las columnas de la tabla
     */
    public function getColumnNames() {
        $query = $this->db->query("DESCRIBE bands");
        $columns = $query->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    }

    /**
     * Obtiene las bandas de la tabla 'bands'
     */
    public function getBands($sql, $filterValue) {
        $query = $this->db->prepare($sql);        

        // Se sanitiza el valor del filtro (los otros datos fueron ya verificados por el controller)
        if (!empty($filterValue)) {
            $filterValue = '%' . $filterValue . '%';
            $query->bindParam(':value', $filterValue, PDO::PARAM_STR);
        }

        $query->execute();

        $albums = $query->fetchAll(PDO::FETCH_OBJ);
        return $albums;
    }

    /**
     * Obtiene la banda con el ID dado
     */
    public function getBandById($id) {
        // Se prepara y ejecuta la consulta
        $sql = 'SELECT * FROM bands WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);

        // Se obtiene y devuelve el resultado
        $band = $query->fetch(PDO::FETCH_OBJ);
        return $band;
    }
}
