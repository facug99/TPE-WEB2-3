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
     * Obtiene los álbumes de la tabla 'albums'
     */
    public function getAlbums($queryParams) {
        $sql = "SELECT * FROM albums";

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
     * Obtiene los álbumes de una banda dada
     */
    public function getAlbumsOfBand($idBand) {
        $sql = 'SELECT * FROM albums WHERE band_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$idBand]);
        $albums = $query->fetchAll(PDO::FETCH_OBJ);
        return $albums;
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
     * Elimina un álbum dado su ID
     */
    public function deleteAlbum($id) {
        $sql = 'DELETE FROM albums WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        return $query->rowCount() > 0;
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
     * Verifica si existe la banda dado su ID. Esto es útil para respetar las
     * restricciones de la clave foránea álbumes-bandas
     */
    public function checkBandExists($band_id) {
        // Consulta SQL para verificar si el band_id existe en la tabla de bandas
        $sql = "SELECT COUNT(*) FROM bands WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->execute([$band_id]);

        // Se obtiene el resultado de la consulta
        $count = $query->fetchColumn();

        // Si count es mayor que 0, significa que el band_id existe
        return $count > 0;
    }
}
