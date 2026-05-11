<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * RESERVATIONS MODEL
 */
class Reservation extends CI_Model {


	// Obtiene las categorías
	public function getCategories($gender = false) {
		if($gender) {
			$this->db->where('gender', $gender);
			$this->db->or_where('gender', NULL);
		}
		$q = $this->db->get('category');
		return ( $q->num_rows() > 0 ) ? $q->result() : false;
	}

	// Agregar nueva reserva
	public function add($data) {
		$data = array_intersect_key($data, array_flip(array('category')));
		return $this->db->insert('reservations', $data) ? $this->db->insert_id() : $this->db->error();
	}

	// Editar reserva
	public function edit($id, $data) {
		$data = array_intersect_key($data, array_flip(array('type', 'court', 'turn')));
		$this->db->where('id', $id)->update('reservations', $data);
		return $this->db->affected_rows();
	}

	// Agregamos participantes a una reserva
	public function addReservationPartners($data) {
		return $this->db->insert_batch('reservations_partners', $data) !== false;
	}

	// Eliminamos participantes de una reserva
	public function deleteReservationPartners($reservation_id) {
		$this->db->delete('reservations_partners', array('reservation_id' => $reservation_id));
		return $this->db->affected_rows();
	}

	// Eliminamos una reserva
	public function delete($id) {
		$this->db->delete('reservations', array('id' => $id));
		return $this->db->affected_rows();
	}

	// Validamos que el usuario pueda hacer una reserva en un determinado día
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
}