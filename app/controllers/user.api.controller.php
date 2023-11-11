<?php

require_once 'app/controllers/api.controller.php';
require_once 'app/models/user.model.php';


class UserApiController extends APIController{
        private $model;

        public __construct(){
            parent::__construct();
            $this->model = new UserModel();
        }

        function getToken($params = []){
            $basic = $this->authHelper->getAuthHeaders();

            if(empty($basic)){
                $this->view->response("No envio encabezados", 401);
            }
            $basic = explode(" ",$basic);
            if($basic[0]!="Basic"){
                $this->view->response("Los encabezados de autenticacion son incorrectos", 401);
                return;
            }
            $userpass = base64_decode($basic[1]);
            $userpass =explode(":", $userpass);

            $user= $userpass[0];
            $password= $userpass[1];
            if($)
        }


}