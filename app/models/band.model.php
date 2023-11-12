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

    /**
     * Obtiene la banda de un álbum dado
     */
    public function getBandOfAlbum($album) {
        $sql = 'SELECT * FROM bands WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$album->band_id]);
        $band = $query->fetch(PDO::FETCH_OBJ);
        return $band;
    }

    /**
     * Inserta una banda en la DB y, si no se produce ningún error, 
     * devuelve un número distinto de 0
     */
    public function insertBand($name, $genre, $location, $year) {
        $sql = 'INSERT INTO bands (name, genre, formed_location, formed_year) VALUES (?, ?, ?, ?)';
        $query = $this->db->prepare($sql);
        $query->execute([$name, $genre, $location, $year]);
        return $this->db->lastInsertId();    
    }

    /**
     * Elimina una banda dado su ID
     */
    public function deleteBand($id, $albums) {
        // Se verifica que la banda no tenga álbumes asociados
        foreach($albums as $album) {
            if ($album->band_id == $id) {
                return false;
            }
        }

        $sql = 'DELETE FROM bands WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        return true;
    }

    /**
     * Modifica una banda dado su ID
     */
    public function editBand($id, $name, $genre, $location, $year) {
        $sql = 'UPDATE bands 
                SET name = ?, genre = ?, formed_location = ?, formed_year = ? 
                WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$name, $genre, $location, $year, $id]);

        // Si la consulta no produjo ningún cambio en la tabla se devuelve false
        $count = $query->rowCount();
        return $count > 0;
    }
    
    /**
     * Verifica si una banda existe en la DB, dado su nombre
     */
    public function checkBandExists($name, $excludedName = null) {
        // Se cuenta la cantidad de registros cuyo nombre coincida el dado
        $sql = "SELECT COUNT(*) FROM bands WHERE name = ?"; 
        if ($excludedName)
            $sql .= " AND name != ?";

        $query = $this->db->prepare($sql);

        if ($excludedName)
            $query->execute([$name, $excludedName]);
        else
            $query->execute([$name]);

        // Se obtiene el resultado de la consulta
        $count = $query->fetchColumn();

        // Si count es mayor que 0, ya existe otra banda con ese nombre
        return $count > 0;
    }
}
