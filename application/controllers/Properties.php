<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Properties extends REST_Controller {
    public function __construct()
    {
        parent::__construct();

        //Cargamos la database
        $this -> load -> database();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,OPTIONS");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        //Cargamos el modelo
        $this -> load -> model('Properties_model');
    }

    public function properties_get() {
        $properties = $this -> Properties_model -> get_properties();
        $this -> load -> library('form_validation');

        if(!isset($properties)) {
            $respuesta = array(
                'error_bool' => TRUE,
                'error' => $this -> form_validation -> get_errores_arreglo(),
                'properties' => array('properties' => null)
            );

            $this -> response($respuesta, Rest_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return ;

        } else {
            $respuesta = array(
                'error_bool' => FALSE,
                'error' => array('err' => 'no'),
                'properties' => $properties
            );
            $this -> response($respuesta);
            return ;
        }

        $properties = $this -> Properties_model -> get_properties();
        $this -> response($properties);
    }

    public function properties_put() {
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
        $this -> form_validation -> set_rules('primary_color', 'color primario', 'required');
        $this -> form_validation -> set_rules('font_size', 'tamanio de fuente', 'required');
        // TRUE: Todo ok, FALSE: Errores de validación
        if ($this -> form_validation -> run()) {
            $properties = $this -> Properties_model -> clean_data($data);
            $respuesta = $properties -> update($properties);
            if ($respuesta['error_bool']) {
                $this -> response($respuesta, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            } else {
                $this -> response($respuesta);
            }
        } else {
            // Validación fallida
            $respuesta = array(
                'error_bool' => TRUE,
                'error' => $this -> form_validation -> get_errores_arreglo(),
                'properties' => array('properties' => null)
            );

            $this -> response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    
}