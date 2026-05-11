<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Partido_model extends CI_Model {

	// Obtener todos los partidos con nombres y gender
	public function getAllWithGender() {
		$q = $this->db
			->select('m.id, m.ronda, m.bracket_pos, m.score, m.fecha, m.hora, m.category, m.gender,
				c.name as categoria,
				p1.name as jugador1, m.jugador1_id,
				p2.name as jugador2, m.jugador2_id,
				g.name as ganador, m.ganador_id')
			->from('matches m')
			->join('category c', 'c.id = m.category')
			->join('partners p1', 'p1.id = m.jugador1_id')
			->join('partners p2', 'p2.id = m.jugador2_id')
			->join('partners g', 'g.id = m.ganador_id', 'left')
			->order_by('m.category ASC, m.gender ASC, m.ronda ASC, m.bracket_pos ASC, m.id ASC')
			->get();
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtener todos los partidos con nombres
	public function getAll() {
		$q = $this->db
			->select('m.id, m.ronda, m.score, m.category, m.gender,
				c.name as categoria,
				p1.name as jugador1, m.jugador1_id,
				p2.name as jugador2, m.jugador2_id,
				g.name as ganador, m.ganador_id')
			->from('matches m')
			->join('category c', 'c.id = m.category')
			->join('partners p1', 'p1.id = m.jugador1_id')
			->join('partners p2', 'p2.id = m.jugador2_id')
			->join('partners g', 'g.id = m.ganador_id', 'left')
			->order_by('m.category ASC, m.ronda ASC, m.id ASC')
			->get();
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtener partidos por categoría (para vista pública)
	public function getByCategory($category_id) {
		$q = $this->db
			->select('m.id, m.ronda, m.score,
				p1.name as jugador1, m.jugador1_id,
				p2.name as jugador2, m.jugador2_id,
				g.name as ganador, m.ganador_id')
			->from('matches m')
			->join('partners p1', 'p1.id = m.jugador1_id')
			->join('partners p2', 'p2.id = m.jugador2_id')
			->join('partners g', 'g.id = m.ganador_id', 'left')
			->where('m.category', $category_id)
			->order_by('m.ronda ASC, m.id ASC')
			->get();
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtener partidos por categoría y género
	public function getByCategoryAndGender($category_id, $gender) {
		$q = $this->db
			->select('m.id, m.ronda, m.bracket_pos, m.score, m.gender,
				p1.name as jugador1, m.jugador1_id,
				p2.name as jugador2, m.jugador2_id,
				g.name as ganador, m.ganador_id')
			->from('matches m')
			->join('partners p1', 'p1.id = m.jugador1_id', 'left')
			->join('partners p2', 'p2.id = m.jugador2_id', 'left')
			->join('partners g', 'g.id = m.ganador_id', 'left')
			->where('m.category', $category_id)
			->where('m.gender', $gender)
			->order_by('m.ronda ASC, m.bracket_pos ASC, m.id ASC')
			->get();
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Agregar partido
	public function add($data) {
		$data = array_intersect_key($data, array_flip(array('category','gender','ronda','bracket_pos','jugador1_id','jugador2_id','score','ganador_id')));
		return $this->db->insert('matches', $data) ? $this->db->insert_id() : false;
	}

	// Editar resultado
	public function edit($id, $data) {
		$data = array_intersect_key($data, array_flip(array('score','ganador_id','ronda','gender','category','jugador1_id','jugador2_id','fecha','hora')));
		// Convertir ganador_id vacío o 0 a NULL
		if(isset($data['ganador_id']) && ($data['ganador_id'] === '' || $data['ganador_id'] === '0' || $data['ganador_id'] === 0)) {
			$data['ganador_id'] = NULL;
		}
		// Convertir score vacío a NULL
		if(isset($data['score']) && $data['score'] === '') {
			$data['score'] = NULL;
		}
		$this->db->where('id', $id)->update('matches', $data);
		return $this->db->affected_rows();
	}

	// Eliminar partido
	public function delete($id) {
		$this->db->delete('matches', array('id' => $id));
		return $this->db->affected_rows();
	}

	// Eliminar todos los partidos de una categoria
	public function deleteByCategory($category_id) {
		$this->db->delete('matches', array('category' => $category_id));
		return $this->db->affected_rows();
	}

	// Eliminar partidos por categoria y genero
	public function deleteByCategoryAndGender($category_id, $gender) {
		$this->db->delete('matches', array('category' => $category_id, 'gender' => $gender));
		return $this->db->affected_rows();
	}

	// Obtener partidos pendientes de un jugador
	public function getPendientesByPlayer($user_id) {
		$sql = "SELECT m.id, m.ronda, m.score, m.gender, m.category,
				c.name as categoria,
				p1.name as jugador1, m.jugador1_id,
				p2.name as jugador2, m.jugador2_id,
				CASE WHEN m.jugador1_id = ? THEN p1.name ELSE p2.name END as yo,
				CASE WHEN m.jugador1_id = ? THEN p2.name ELSE p1.name END as rival,
				CASE WHEN m.jugador1_id = ? THEN m.jugador1_id ELSE m.jugador2_id END as yo_id
				FROM matches m
				JOIN category c ON c.id = m.category
				JOIN partners p1 ON p1.id = m.jugador1_id
				JOIN partners p2 ON p2.id = m.jugador2_id
				WHERE (m.jugador1_id = ? OR m.jugador2_id = ?)
				AND m.ganador_id IS NULL
				ORDER BY m.id ASC";
		$q = $this->db->query($sql, array($user_id, $user_id, $user_id, $user_id, $user_id));
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtener partido por ID
	public function getById($id) {
		$q = $this->db
			->select('m.*, c.name as categoria')
			->from('matches m')
			->join('category c', 'c.id = m.category')
			->where('m.id', $id)
			->get();
		return ($q->num_rows() > 0) ? $q->row() : null;
	}

	// Obtener partidos de una ronda específica ordenados por id
	public function getByRonda($category_id, $gender, $ronda) {
		$q = $this->db
			->select('m.id, m.jugador1_id, m.jugador2_id, m.ganador_id, m.ronda, m.category, m.gender')
			->from('matches m')
			->where('m.category', $category_id)
			->where('m.gender', $gender)
			->where('m.ronda', $ronda)
			->order_by('m.id ASC')
			->get();
		return ($q->num_rows() > 0) ? $q->result() : null;
	}

	// Verificar si ya existe un partido en una ronda entre dos jugadores
	public function existePartido($category_id, $gender, $ronda, $j1, $j2) {
		$q = $this->db
			->from('matches')
			->where('category', $category_id)
			->where('gender', $gender)
			->where('ronda', $ronda)
			->group_start()
				->where('jugador1_id', $j1)->where('jugador2_id', $j2)
			->group_end()
			->or_group_start()
				->where('jugador1_id', $j2)->where('jugador2_id', $j1)
			->group_end()
			->get();
		return $q->num_rows() > 0;
	}

	// Insertar múltiples partidos
	public function addBatch($data) {
		if(empty($data)) return false;
		return $this->db->insert_batch('matches', $data) !== false;
	}
}
