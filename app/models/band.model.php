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
    public function getBands($queryParams) {
        $sql = "SELECT * FROM bands";

        // Filtro
        if (!empty($queryParams['filter']) && !empty($queryParams['value']))
            $sql .= ' WHERE ' . $queryParams['filter'] . ' LIKE \'%' . $queryParams['value'] . '%\'';

        // Ordenamiento
        if (!empty($queryParams['sort'])) {
            $sql .= ' ORDER BY '. $queryParams['sort'];

            // Orden ascendente y descendente
            if (!empty($queryParams['order']))
                $sql .= ' ' . $queryParams['order'];
        }

        // Paginación
        if (!empty($queryParams['limit']))
            $sql .= ' LIMIT ' . $queryParams['limit'] . ' OFFSET ' . $queryParams['offset'];

        // No hace falta sanitizar consulta (datos ingresados ya fueron verificados por el controlador)
        $query = $this->db->prepare($sql);        
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
