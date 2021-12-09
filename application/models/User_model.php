<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_model{

    public $id;
    public $name;
    public $password;
    public $isActive;
    public $isAdmin;

    public function get_user($name, $password) {
        $this -> db -> reset_query();
        $this -> db -> where(array('name' => $name, 'password' => $password, 'isActive' => 1));
        $query = $this -> db -> get('users');

        $result = $query -> custom_row_object(0, 'User_model');
        
        if (isset($result)) { // Si tiene datos...
            // Cambio los datos del usuario a los originales
            $result -> id = intval($result -> id);
            $result -> isActive = boolval($result -> isActive);
            $result -> isAdmin = boolval($result -> isAdmin);

            return $result;
        } else {
            return null;
        }
    }

    public function get_all_user()
    {
        $query = $this->db->get('users');
        $result = $query->custom_result_object('User_model');

        foreach ($result as $row)
        {
            if (isset($row)) {
                $row->id = intval($row->id);
                $row->isActive = boolval($row->isActive);
                $row->isAdmin = boolval($row->isAdmin);
            }
        }
        
        return $result;
    }

    public function update($user){
        $query = $this -> db -> get_where('users', array('id' => $this -> id));
        $route_id = $query -> row();
        
        //Comprobamos si existe
        $this -> db -> reset_query(); //reseteo las consultas
        $this-> db ->where('id', $this -> id); //Le paso la id al update  y listo
        if (isset($route_id) && $this -> db -> update('users',$this)) {
            // Se actualiza correctamente
            $response = array(
                'error_bool' => FALSE,
                'error' => array('err' => 'no'),
                'user' => $this,
            );

            return $response;
        } else {
            // No se pudo actualizar
            $response = array(
                'error_bool' => TRUE,
                'error' => $this -> db -> error_message(),
                'user' => array('user' => null),
            );

            return $response;
        }
    }


    public function clean_data($dirty_data)
    {
        //Vamos a coger los datos que nos vengas ya sea put o post y limpiar los campos que no existan
        foreach ($dirty_data as $name_attribute => $value) {
            if (property_exists('User_model', $name_attribute)) {
                $this->$name_attribute = $value;
            }
        }

        //Si activo es null, por defecto a 1
        if ($this-> isActive == null) {
            $this-> isActive = 1;
        }
        return $this;
    }
}