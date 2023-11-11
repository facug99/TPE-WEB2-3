<?php

require_once "app/models/model.php";

class UserModel extends Model {
    /**
     * Obtiene un usuario dado su nombre de usuario
     */
    public function getUserByUsername($username) {
        $query = $this->db->prepare('SELECT * FROM users WHERE username = ?');
        $query->execute([$username]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
}