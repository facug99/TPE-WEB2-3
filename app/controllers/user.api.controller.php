<?php

require_once 'app/controllers/api.controller.php';
require_once 'app/models/user.model.php';
require_once 'app/helpers/auth.api.helper.php';

class UserApiController extends APIController {
    private $model;
    private $authHelper;

    public function __construct() {
        parent::__construct();
        $this->model = new UserModel();
        $this->authHelper = new AuthHelper();
    }

    function getToken($params = []) {
        $basic = $this->authHelper->getAuthHeaders();

        if (empty($basic)) {
            $this->view->response("No authentication headers sent", 401);
            return;
        }

        $basic = explode(" ", $basic);
        if ($basic[0] != "Basic") {
            $this->view->response("Invalid authentication headers", 401);
            return;
        }

        $userpass = base64_decode($basic[1]);
        $userpass = explode(":", $userpass);

        $user = $userpass[0];
        $password = $userpass[1];

        // Se obtiene el usuario de la base de datos
        $userData = $this->model->getUserByUsername($user);

        // Se verifica usuario y contraseña
        if ($user == $userData->username && password_verify($password, $userData->password)) {
            $token = $this->authHelper->createToken($userData);
            $this->view->response($token, 200);
        } else
            $this->view->response('Invalid username or password', 401);
    }
}
