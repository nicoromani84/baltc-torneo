<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reservation extends CI_Model {

	public function getCategories($gender = false) {
		if($gender) {
			$this->db->where('gender', $gender);
			$this->db->or_where('gender', NULL);
		}
		$q = $this->db->get('category');
		return ( $q->num_rows() > 0 ) ? $q->result() : false;
	}

	public function add($data) {
		$data = array_intersect_key($data, array_flip(array('category')));
		return $this->db->insert('reservations', $data) ? $this->db->insert_id() : $this->db->error();
	}

	public function edit($id, $data) {
		$data = array_intersect_key($data, array_flip(array('type', 'court', 'turn')));
		$this->db->where('id', $id)->update('reservations', $data);
		return $this->db->affected_rows();
	}

	public function addReservationPartners($data) {
		return $this->db->insert_batch('reservations_partners', $data) !== false;
	}

	public function deleteReservationPartners($reservation_id) {
		$this->db->delete('reservations_partners', array('reservation_id' => $reservation_id));
		return $this->db->affected_rows();
	}

	public function delete($id) {
		$this->db->delete('reservations', array('id' => $id));
		return $this->db->affected_rows();
	}

	public function canReserve($partners) {
		$q = $this->db
			->select('p.id, p.name')
			->join('reservations r', 'rp.reservation_id = r.id')
			->join('partners p', 'rp.partner_id = p.id')
			->where_in('p.id', $partners)
			->group_by('p.id')
			->get('reservations_partners rp');
		return ($q->num_rows() > 0) ? $q->result() : false;
	}

	// Obtiene el nombre de una categoría por ID
	public function getCategoryName($category_id) {
		$q = $this->db->where('id', $category_id)->get('category');
		if($q->num_rows() > 0) return $q->row()->name;
		return '';
	}

	// Verifica si un jugador ya está inscripto
	public function isPlayerRegistered($user_id) {
		$q = $this->db
			->from('reservations_partners')
			->where('partner_id', $user_id)
			->get();
		return $q->num_rows() > 0;
	}

	// Obtiene la categoría en la que juega un usuario
	public function getCategoryByPlayer($user_id) {
		$q = $this->db->query(
			"SELECT r.category FROM reservations r
			JOIN reservations_partners rp ON rp.reservation_id = r.id
			WHERE rp.partner_id = ? LIMIT 1",
			array(intval($user_id))
		);
		if($q && $q->num_rows() > 0) {
			$row = $q->row();
			return $row->category;
		}
		return null;
	}

	// Obtiene todos los inscriptos agrupados por categoría
	public function getResultados() {
		$sql = "SELECT DISTINCT p.name as jugador, p.gender, c.name as categoria, c.id as categoria_id
				FROM reservations r
				JOIN reservations_partners rp ON rp.reservation_id = r.id
				JOIN partners p ON p.id = rp.partner_id
				JOIN category c ON c.id = r.category
				ORDER BY c.id ASC, p.gender ASC, p.name ASC";
		$q = $this->db->query($sql);
		return ($q->num_rows() > 0) ? $q->result() : false;
	}
}
