<?php

require_once 'app/controllers/api.controller.php';
require_once 'app/models/user.model.php';
require_once 'app/helpers/auth.api.helper.php';


class UserApiController extends APIController{
        private $model;
        private $authHelper;

        public function __construct(){
            parent::__construct();
            $this->model = new UserModel();
            $this->authHelper = new AuthHelper();
        }

        function getToken($params = []){
            $basic = $this->authHelper->getAuthHeaders();

            if(empty($basic)){
                $this->view->response("No envio encabezados", 401);
                return;
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

                $userdata = $this->model->getUserByUsername($user);
                $userlog = $userdata->username;
                $userlogpass = $userdata->password;
           

                if($user == $userlog && $password == $userlogpass ) {
                    $token = $this->authHelper->createToken($userdata);
                    $this->view->response($token);
                }else {
                        $this->view->response('El usuario o contraseña son incorrectos.', 401);
                }
            

        }


}