<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Login extends REST_Controller {

    public function __construct() {
        //Llamamos al constructor padre
        parent :: __construct();

        //Cargamos la database
        $this -> load -> database();

        //Cargamos el modelo
        $this -> load -> model('User_model');
    }

    //Esto es para obtener los resultados
    public function user_get() {
        $user_name = $this -> uri -> segment(3);
        $user_password = $this -> uri -> segment(4);

        //Validar el parámetro
        if (!isset($user_name) || !isset($user_password)) {
            $respuesta = array(
                'error' => array('err' => 'Parámetros incorrectos o faltantes'),
                'user' => null
            );
            $this -> response($respuesta, Rest_Controller::HTTP_BAD_REQUEST);
            return ;
        }

        $user = $this -> User_model -> get_user($user_name, $user_password);
        //Validamos el user
        if ($user == null) {
            $respuesta = array(
                'error' => array('err' => 'El usuario no existe'),
                'user' => null
            );

            $this -> response($respuesta, Rest_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return ;

        } else {
            $respuesta = array(
                'error' => null,
                'user' => $user
            );

            $this -> response($respuesta);
            return ;
        }

        $user = $this -> User_model -> get_user($user_name, $user_password);

        //Codeigniter ya tiene un metodo para dar la respuesta en JSON automatico
        $this -> response($user);
    }

    public function user_put() {
        // Cojo los datos que nos pasas por el POST
        $data = $this -> put();

         //Si el data esta vacio, le definimos un parametro incorrecto 
        //Esto nos servira para que aplique las reglas del form_validation
        if (empty($data)) $data = array('error' => 'error');
        // Cargo la librería form_validation que trae CodeIgniter.
        $this -> load -> library('form_validation');
        // Le digo al form validation, que datos debe validar
        $this -> form_validation -> set_data($data);
        // Aplico la validación con campo, etiqueta y regla.
        $this -> form_validation -> set_rules('id', 'id', 'required');
        $this -> form_validation -> set_rules('name', 'nombre', 'required');
        $this -> form_validation -> set_rules('password', 'contrasenia', 'required');
        $this -> form_validation -> set_rules('isActive', 'estaActivo', 'required');
        $this -> form_validation -> set_rules('isAdmin', 'esAdministrador', 'required');
        // TRUE: Todo ok, FALSE: Errores de validación
        if ($this -> form_validation -> run()) {
            $user = $this -> User_model -> clean_data($data);
            $respuesta = $user -> update($user);
            if ($respuesta['error'] == null) {
                $this -> response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $this -> response($respuesta);
            }
        } else {
            // Validación fallida
            $respuesta = array(
                'error' => $this -> form_validation -> get_errores_arreglo(),
                'user' => null
            );

            $this -> response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}