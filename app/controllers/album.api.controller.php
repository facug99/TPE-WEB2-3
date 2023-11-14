<?php

require_once 'app/controllers/api.controller.php';
require_once 'app/models/album.model.php';
require_once 'app/models/band.model.php';
require_once 'app/helpers/auth.api.helper.php';

class AlbumAPIController extends APIController {
    private $albumModel;
    private $bandModel;
    private $authHelper;

    public function __construct() {
        parent::__construct();
        $this->albumModel = new AlbumModel();
        $this->bandModel = new BandModel();
        $this->authHelper = new AuthHelper();
    }

    /**
     * Crea un álbum con los atributos pasados por JSON
     */
    public function create() {
        // Se verifica autenticación/autorización del usuario
        $user = $this->authHelper->currentUser();
        if (!$user) {
            $this->view->response('Unauthorized', 401);
            return;
        }

        // Se obtienen los datos enviados por POST
        $body = $this->getData();
        $title = $body->title;
        $year = $body->year;
        $bandId = $body->band_id;

        // Se verifica si existe la banda
        $band = $this->bandModel->getBandById($bandId);
        if (empty($band)) {
            $this->view->response("Band id=$bandId does not exist", 422);
            return;
        }

        // Se crea el álbum en la base de datos e informa la vista el resultado
        $id = $this->albumModel->insertAlbum($title, $year, $bandId);
        if ($id)
            $this->view->response("Album id=$id successfully created", 201);
        else
            $this->view->response("Album id=$id could not be created", 422);
    }

    /**
     * Devuelve un arreglo con los álbumes de la base de datos según determinados parámetros de consulta
     */
    public function getAll() {
        // Se obtienen nombres de columnas de la tabla para futuras verificaciones
        $columns = $this->albumModel->getColumnNames();

        // Arreglo donde se almacenarán los parámetros de consulta
        $queryParams = array();

        // Filtro
        $queryParams += $this->handleFilter($columns);

        // Ordenamiento
        $queryParams += $this->handleSort($columns);

        // Paginación
        $queryParams += $this->handlePagination();

        // Generación de sentencia SQL a partir de parámetros de consulta
        $sql = $this->buildSqlQuery("albums", $queryParams);

        // Se obtienen los álbumes y se devuelven en formato JSON
        $albums = $this->albumModel->getAlbums($sql, $queryParams['value']);
        return $this->view->response($albums, 200);
    }

    /**
     * Devuelve el JSON de un álbum con ID específico
     */
    public function get($params = []) {
        $id = $params[':id'];
        $album = $this->albumModel->getAlbumById($id);
 
        if (!empty($album))
            return $this->view->response($album, 200);
        else
            return $this->view->response("Album id=$id not found", 404);
    }

    /**
     * Se modifica un álbum dado su ID
     */
    public function update($params = []) {
        // Se verifica autenticación/autorización del usuario
        $user = $this->authHelper->currentUser();
        if (!$user) {
            $this->view->response('Unauthorized', 401);
            return;
        }

        // Si no se especifica el ID de álbum, se produce un error
        if (empty($params)) {
            $this->view->response("Album not specified", 400);
            return;
        }

        $id = $params[':id'];
        $album = $this->albumModel->getAlbumById($id);

        // Se verifica si existe el álbum a modificar
        if (empty($album)) {
            $this->view->response("Album id=$id not found", 404);
            return;
        }

        // Se obtienen los datos enviados por PUT
        $body = $this->getData();
        $title = $body->title;
        $year = $body->year;
        $bandId = $body->band_id;

        // Se verifica si el nuevo ID de banda existe
        $band = $this->bandModel->getBandById($bandId);
        if (empty($band)) {
            $this->view->response("Band id=$bandId does not exist", 422);
            return;
        }

        // Se modifica el álbum y se informa a la vista
        $modified = $this->albumModel->editAlbum($id, $title, $year, $bandId);
        if ($modified)
            $this->view->response("Album id=$id successfully modified", 200);
        else
            $this->view->response("Album id=$id could not be modified", 422);
    }

    /**
     * Elimina un álbum dado su ID
     */
    public function delete($params = []) {
        // Se verifica autenticación/autorización del usuario
        $user = $this->authHelper->currentUser();
        if (!$user) {
            $this->view->response('Unauthorized', 401);
            return;
        }

        // Si no se especifica el ID del álbum se produce un error
        if (empty($params)) {
            $this->view->response("Album not specified", 400);
            return;
        }

        $id = $params[':id'];
        $album = $this->albumModel->getAlbumById($id);

        // Se verifica si existe el álbum a eliminar
        if ($album) {
            $this->albumModel->deleteAlbum($id);
            $this->view->response("Album id=$id deleted", 200);
        } else
            $this->view->response("Album id=$id not found", 404);
    }
}
