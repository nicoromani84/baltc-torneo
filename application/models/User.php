<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * USER MODEL
 */
class User extends CI_Model 
{
	//Obtiene todos los usuarios
	public function getAll() {
		//Buscamos todos los usuarios
		$q = $this->db->get('partners');
		//Retornamos
		return ( $q->num_rows() > 0 ) ? $q->result() : false;
	}

	//Obtiene todos los usuarios excepto el usuario logueado
	public function getAllExceptMe($gender = false) {
		//Buscamos todos los usuarios
		$this->db->select('*');
		if($this->isLogged())
			$this->db->where('id !=', $this->session->id, false);
		if($gender)
			$this->db->where('gender', $gender);
		$q = $this->db->get('partners');
		//Retornamos
		return ( $q->num_rows() > 0 ) ? $q->result() : false;
	}

	//Obtiene usuario por id
	public function getByID($id) {
		$q = $this->db->where('id', $id)->get('partners');
		return ( $q->num_rows() == 1 ) ? $q->row() : false;
	}

	//chequea existencia del usuario
	public function check( $dni ) {
		// Remover puntos del DNI para buscar
		$dni_clean = preg_replace('/\D/', '', $dni);
		//Buscamos el usuario y la contraseña
		$q = $this->db->select('*')
			->where('dni',$dni_clean)
			->get('partners');
		//Retornamos
		return ( $q->num_rows() == 1 ) ? $q->row() : false;
	}

	//Chequea si el usuario está loggeado
	public function isLogged() {
		return $this->session->userdata('isLogged');
	}
}
