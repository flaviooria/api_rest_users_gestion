<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Routes_model extends CI_Model
{
    public $id;
    public $page;
    public $icon;
    public $active;
    public $subtitle;
    public $text;

    public function get_routes() // Todo hecho funciona bien como provider
    {
        $query = $this->db->get('routes');
        $result = $query->custom_result_object('Routes_model');

        foreach ($result as $row)
        {
            if (isset($row)) {
                $row->id = intval($row->id);
                $row->active = boolval($row->active);
            }
        }
        
        return $result;
    }

    public function insert($route)
    {
        $query = $this->db->get_where('routes', array('id' => $this->id));
        $route_id = $query->row();
        // El routes ya existe en BD
        if (isset($route_id)) {
            $respuesta = array(
                'error_bool' => TRUE,
                'error' => array('err' => 'ruta ya existe en la base de datos'),
                'routes' => array('routes' => null),
            );
            return $respuesta;
        } else {
            // Limpiamos los datos antes de insertarlos
            //$routes = $this -> routes_model -> limpiar_datos($data);

            // Insertamos el registro
            if ($this->db->insert('routes', $this)) {
                // Se insertó
                $respuesta = array(
                    'error_bool' => FALSE,
                    'error' => array('err' => 'no'),
                    'routes' => $this,
                );
            } else {
                // No se puede insertar
                $respuesta = array(
                    'error_bool' => TRUE,
                    'error' => array('err' => 'no se pudo insertar ruta en la base de datos'),
                    'routes' => array('routes' => null),
                );
            }
            return $respuesta;
        }
    }

    public function update($route)
    {
        $query = $this -> db -> get_where('routes', array('id' => $this -> id));
        $route_id = $query -> row();
        
        //Comprobamos si existe
        $this -> db -> reset_query(); //reseteo las consultas
        $this-> db ->where('id', $this -> id); //Le paso la id al update  y listo
        if (isset($route_id) && $this -> db -> update('routes',$this)) {
            // Se actualiza correctamente
            $response = array(
                'error_bool' => FALSE,
                'error' => array('err' => 'no'),
                'routes' => $this,
            );

            return $response;
        } else {
            // No se pudo actualizar
            $response = array(
                'error_bool' => TRUE,
                'error' =>  array('err' => 'no se pudo actualizar la ruta en la base de datos'),
                'routes' => array('routes' => -1),
            );

            return $response;
        }
    }

    public function clean_data($dirty_data)
    {
        //Vamos a coger los datos que nos vengas ya sea put o post y limpiar los campos que no existan
        foreach ($dirty_data as $name_attribute => $value) {
            if (property_exists('Routes_model', $name_attribute)) {
                $this->$name_attribute = $value;
            }
        }

        //Si activo es null, por defecto a 1
        if ($this->active == null) {
            $this->active = 1;
        }
        return $this;
    }
}
