<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ADMIN MODEL
 */
class Administrator extends CI_Model 
{
	//Obtiene usuario por id
	public function getByID($id) {
		$q = $this->db->where('id', $id)->get('admin');
		return ( $q->num_rows() == 1 ) ? $q->row() : false;
	}

	//chequea existencia del usuario
	public function check( $username, $password ) {
		//Buscamos el usuario y la contraseña
		$q = $this->db->select('*')
			->where('username',$username)
			->where('password',sha1($password))
			->get('admin');
		//Retornamos
		return ( $q->num_rows() == 1 ) ? $q->row() : false;
	}

	//Chequea si el usuario está loggeado
	public function isLogged() {
		return $this->session->userdata['admin']['isLogged'];
	}

	//Obtiene las reservas para el dashboard
	public function getReservations($gender = false, $category = false) {
		$this->db
		->select('res.id, res.timestamp, cat.name category,
		CONCAT(\'[\', GROUP_CONCAT((SELECT CONCAT(\'{"id":"\', spart.id, \'", "name":"\', spart.name, \'", "email":"\', spart.email,\'"}\') FROM partners spart WHERE spart.id = rp.partner_id) ORDER BY rp.id SEPARATOR \',\'), \']\') partners')
		->join('reservations_partners rp', 'res.id = rp.reservation_id', 'left')
		->join('category cat', 'res.category = cat.id')
		->group_by('res.id');
		if($category) {
			$this->db->where('res.category', $category);
		}
		if($gender) {
			$this->db->join('partners p', 'rp.partner_id = p.id');
			$this->db->where('p.gender', $gender);
		}

		$q = $this->db->get('reservations res');

		return ( $q->num_rows() > 0 ) ? $q->result() : false;
	}
}
