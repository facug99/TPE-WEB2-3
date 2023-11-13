<?php

require_once "app/models/model.php";

class AlbumModel extends Model {
    /**
     * Devuelve el nombre de las columnas de la tabla
     */
    public function getColumnNames() {
        $query = $this->db->query("DESCRIBE albums");
        $columns = $query->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    }

    /**
     * Inserta un álbum en la DB y, si no se produce ningún error, 
     * devuelve un número distinto de 0
     */
    public function insertAlbum($title, $year, $band_id) {
        $sql = 'INSERT INTO albums (title, year, band_id) VALUES (?, ?, ?)';
        $query = $this->db->prepare($sql);
        $query->execute([$title, $year, $band_id]);
        return $this->db->lastInsertId();
    }

    /**
     * Obtiene los álbumes de la tabla 'albums'
     */
    public function getAlbums($queryParams) {
        $sql = "SELECT * FROM albums";

        // Filtro
        if (!empty($queryParams['filter']) && !empty($queryParams['value']))
            $sql .= ' WHERE ' . $queryParams['filter'] . ' LIKE :value';

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

        
        $query = $this->db->prepare($sql);        

        // Se sanitiza el valor del filtro (queryParams['value']). 
        // Los otros datos fueron verificados por el controller
        if (!empty($queryParams['value'])) {
            $value = '%' . $queryParams['value'] . '%';
            $query->bindParam(':value', $value, PDO::PARAM_STR);
        }

        $query->execute();

        $albums = $query->fetchAll(PDO::FETCH_OBJ);
        return $albums;
    }

    /**
     * Obtiene el álbum con el ID dado
     */
    public function getAlbumById($id) {
        $sql = 'SELECT * FROM albums WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        $album = $query->fetch(PDO::FETCH_OBJ);
        return $album;
    }

    /**
     * Modifica una banda dado su ID
     */
    public function editAlbum($id, $title, $year, $band_id) {
        $sql = 'UPDATE albums 
                SET title = ?, year = ?, band_id = ? 
                WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$title, $year, $band_id, $id]);
        $count = $query->rowCount();
        return $count > 0;
    }

    /**
     * Elimina un álbum dado su ID
     */
    public function deleteAlbum($id) {
        $sql = 'DELETE FROM albums WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        return $query->rowCount() > 0;
    }
}
