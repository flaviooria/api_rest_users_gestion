<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Routes extends REST_Controller {
    public function __construct()
    {
        parent::__construct();

        //Cargamos la database
        $this -> load -> database();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET");

        //Cargamos el modelo
        $this -> load -> model('Routes_model');
    }

    public function routes_get() {
        $routes = $this -> Routes_model -> get_routes();
        $this -> load -> library('form_validation');

        if(!isset($routes)) {
            $respuesta = array(
                'error_bool' => TRUE,
                'error' => $this -> form_validation -> get_errores_arreglo(),
                'routes' => null
            );
            $this -> response($respuesta, Rest_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return $respuesta;
        } else {
            $respuesta = array(
                'error_bool' => FALSE,
                'error' => array('err' => 'no'),
                'routes' => $routes
            );
            $this -> response($respuesta);
            return ;
        }

        $routes = $this -> Routes_model -> get_routes();
        $this -> response($routes);
    }

    public function routes_post() {
        // Cojo los datos que nos pasas por el POST
        $data = $this -> post();
        // Esto es por si nos mandan un objeto vacio que el formulario actúe
        if (empty($data)) $data = array('error' => 'error');
        // Cargo la librería form_validation que trae CodeIgniter.
        $this -> load -> library('form_validation');
        // Le digo al form validation, que datos debe validar
        $this -> form_validation -> set_data($data);
        // Aplico la validación con campo, etiqueta y regla.
        $this -> form_validation -> set_rules('active', 'activo', 'required');
        $this -> form_validation -> set_rules('icon', 'icono', 'required');
        $this -> form_validation -> set_rules('page', 'pagina', 'required');
        $this -> form_validation -> set_rules('text', 'texto', 'required');
        // TRUE: Todo ok, FALSE: Errores de validación
        if ($this -> form_validation -> run()) {
            $route = $this -> Routes_model -> clean_data($data);
            $respuesta = $route -> insert($route);
            if ($respuesta['error']) {
                $this -> response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $this -> response($respuesta);
            }
        } else {
            // Validación fallida
            $respuesta = array(
                'error_bool' => TRUE,
                'error' => $this -> form_validation -> get_errores_arreglo(),
                'routes' => null
            );

            $this -> response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function routes_put() {
        // Cojo los datos que nos pasas por el POST
        $data = $this -> put();
        // Esto es por si nos mandan un objeto vacio que el formulario actúe
        if (empty($data)) $data = array('error' => 'error');
        // Cargo la librería form_validation que trae CodeIgniter.
        $this -> load -> library('form_validation');
        // Le digo al form validation, que datos debe validar
        $this -> form_validation -> set_data($data);
        // Aplico la validación con campo, etiqueta y regla.
        $this -> form_validation -> set_rules('active', 'activo', 'required');
        $this -> form_validation -> set_rules('icon', 'icono', 'required');
        $this -> form_validation -> set_rules('page', 'pagina', 'required');
        $this -> form_validation -> set_rules('text', 'texto', 'required');
        $this -> form_validation -> set_rules('id', 'identificador', 'required');
        // TRUE: Todo ok, FALSE: Errores de validación
        if ($this -> form_validation -> run()) {
            $route = $this -> Routes_model -> clean_data($data);
            $respuesta = $route -> update($route);
            if ($respuesta['error']) {
                $this -> response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $this -> response($respuesta);
            }
        } else {
            // Validación fallida
            $respuesta = array(
                'error_bool' => TRUE,
                'error' => $this -> form_validation -> get_errores_arreglo(),
                'routes' => null,
            );

            $this -> response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}