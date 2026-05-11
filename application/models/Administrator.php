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

	// Obtiene lista de inscriptos para los selects de partidos
	public function getInscriptos() {
		$q = $this->db
			->distinct()
			->select('p.id as jugador_id, p.name as jugador, c.id as category_id, c.name as categoria')
			->from('reservations r')
			->join('reservations_partners rp', 'rp.reservation_id = r.id')
			->join('partners p', 'p.id = rp.partner_id')
			->join('category c', 'c.id = r.category')
			->order_by('p.name ASC')
			->get();
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Inscriptos filtrados por categoría y género
	public function getInscriptosByCategory($category_id, $gender = false) {
		$this->db
			->distinct()
			->select('p.id as jugador_id, p.name as jugador, p.gender')
			->from('reservations r')
			->join('reservations_partners rp', 'rp.reservation_id = r.id')
			->join('partners p', 'p.id = rp.partner_id')
			->where('r.category', $category_id);
		if($gender) {
			$this->db->where('p.gender', $gender);
		}
		$q = $this->db->order_by('p.name ASC')->get();
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtiene todos los inscriptos con datos completos
	public function getJugadores() {
		$sql = "SELECT DISTINCT p.id, p.name, p.dni, p.email, p.gender,
				c.id as category_id, c.name as categoria,
				r.id as reserva_id
				FROM reservations r
				JOIN reservations_partners rp ON rp.reservation_id = r.id
				JOIN partners p ON p.id = rp.partner_id
				JOIN category c ON c.id = r.category
				ORDER BY r.id DESC";
		$q = $this->db->query($sql);
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Editar jugador (datos del partner + categoria de la reserva)
	public function editJugador($partner_id, $reserva_id, $data) {
		// Actualizar datos del partner
		$partner_data = array_intersect_key($data, array_flip(array('name','dni','email','gender')));
		$this->db->where('id', $partner_id)->update('partners', $partner_data);
		// Actualizar categoria de la reserva
		if(!empty($data['category'])) {
			$this->db->where('id', $reserva_id)->update('reservations', array('category' => $data['category']));
		}
		return true;
	}

	// Agregar jugador nuevo e inscribirlo
	public function addJugador($data) {
		// Insertar partner
		$partner = array(
			'name'   => $data['name'],
			'dni'    => $data['dni'],
			'email'  => $data['email'],
			'gender' => $data['gender'],
			'status' => 'ACTIVO'
		);
		$this->db->insert('partners', $partner);
		$partner_id = $this->db->insert_id();

		// Crear reserva
		$this->db->insert('reservations', array('category' => $data['category']));
		$reserva_id = $this->db->insert_id();

		// Vincular
		$this->db->insert('reservations_partners', array(
			'partner_id'     => $partner_id,
			'reservation_id' => $reserva_id
		));
		return $partner_id;
	}

	// Eliminar jugador del torneo
	public function deleteJugador($partner_id, $reserva_id) {
		$this->db->delete('reservations_partners', array('partner_id' => $partner_id, 'reservation_id' => $reserva_id));
		$this->db->delete('reservations', array('id' => $reserva_id));
		return true;
	}
// ── CATEGORÍAS ──────────────────────────────────────────

	public function getAllCategories() {
		$q = $this->db->order_by('id ASC')->get('category');
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	public function addCategory($name) {
		$this->db->insert('category', array('name' => $name));
		return $this->db->insert_id();
	}

	public function editCategory($id, $name) {
		$this->db->where('id', $id)->update('category', array('name' => $name));
		return $this->db->affected_rows() > 0;
	}

	public function deleteCategory($id) {
		$this->db->where('id', $id)->delete('category');
		return $this->db->affected_rows() > 0;
	}
}
