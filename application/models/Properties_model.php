<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Properties_model extends CI_Model {
    public $id;
    public $primary_color;
    public $font_size;

    public function get_properties()
    {
        $query = $this -> db -> get('properties');

        $result = $query -> custom_row_object(0,'Properties_model');

        if(isset($result)) {
            $result -> id = intval($result -> id);
            $result -> font_size = floatval(($result -> font_size));

            return $result;
        } else {
            return null;
        }

        
    }

    public function update($properties)
    {
        $query = $this -> db -> get_where('properties', array('id' => $this -> id));
        $route_id = $query -> row();
        
        //Comprobamos si existe
        $this -> db -> reset_query(); //reseteo las consultas
        $this-> db ->where('id', $this -> id); //Le paso la id al update  y listo
        if (isset($route_id) && $this -> db -> update('properties',$this)) {
            // Se actualiza correctamente
            $response = array(
                'error_bool' => FALSE,
                'error' => array('err' => 'no'),
                'properties' => $this
            );

            return $response;
        } else {
            // No se pudo actualizar
            $response = array(
                'error_bool' => TRUE,
                'error' => $this->db->error_message(),
                'properties' => null
            );

            return $response;
        }
    }

    public function clean_data($dirty_data)
    {
        //Vamos a coger los datos que nos vengas ya sea put o post y limpiar los campos que no existan
        foreach ($dirty_data as $name_attribute => $value) {
            if (property_exists('Properties_model', $name_attribute)) {
                $this->$name_attribute = $value;
            }
        }

        return $this;
    }
}