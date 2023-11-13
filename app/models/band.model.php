<?php

require_once "app/models/model.php";

class BandModel extends Model {
    /**
     * Obtiene las bandas de la tabla 'bands'
     */
    public function getBands() {
        // Se prepara y ejecuta la consulta
        $sql = 'SELECT * FROM bands';
        $query = $this->db->prepare($sql);
        $query->execute();

        // Se obtienen y devuelven los resultados
        $bands = $query->fetchAll(PDO::FETCH_OBJ);
        return $bands;
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
