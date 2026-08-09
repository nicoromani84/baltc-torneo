<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Partido_model extends CI_Model {

	// Obtener todos los partidos con nombres y gender
	public function getAllWithGender() {
		$sql = "SELECT m.id, m.ronda, m.bracket_pos, m.score, m.fecha, m.hora, m.category, m.gender,
				c.name as categoria,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador1_id) as jugador1, m.jugador1_id,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador2_id) as jugador2, m.jugador2_id,
				CASE
					WHEN m.jugador2_id IS NULL THEN (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.jugador1_id)
					WHEN m.ganador_id IS NULL THEN ''
					ELSE (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.ganador_id)
				END as ganador, m.ganador_id
			FROM matches m
			LEFT JOIN category c ON c.id = m.category
			ORDER BY m.category ASC, m.gender ASC, m.ronda ASC, m.bracket_pos ASC, m.id ASC";
		$q = $this->db->query($sql);
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtener todos los partidos con nombres
	public function getAll() {
		$sql = "SELECT m.id, m.ronda, m.score, m.category, m.gender, m.fecha, m.hora, m.deadline,
				c.name as categoria,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador1_id) as jugador1, m.jugador1_id,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador2_id) as jugador2, m.jugador2_id,
				CASE
					WHEN m.jugador2_id IS NULL THEN (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.jugador1_id)
					WHEN m.ganador_id IS NULL THEN ''
					ELSE (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.ganador_id)
				END as ganador, m.ganador_id
			FROM matches m
			LEFT JOIN category c ON c.id = m.category
			ORDER BY m.category ASC, m.ronda ASC, m.id ASC";
		$q = $this->db->query($sql);
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtener partidos por categoría (para vista pública)
	public function getByCategory($category_id) {
		$sql = "SELECT m.id, m.ronda, m.score,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador1_id) as jugador1, m.jugador1_id,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador2_id) as jugador2, m.jugador2_id,
				CASE
					WHEN m.jugador2_id IS NULL THEN (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.jugador1_id)
					WHEN m.ganador_id IS NULL THEN ''
					ELSE (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.ganador_id)
				END as ganador, m.ganador_id
			FROM matches m
			WHERE m.category = " . intval($category_id) . "
			ORDER BY m.ronda ASC, m.id ASC";
		$q = $this->db->query($sql);
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtener partidos por categoría y género
	public function getByCategoryAndGender($category_id, $gender) {
		$sql = "SELECT m.id, m.ronda, m.bracket_pos, m.score, m.gender,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador1_id) as jugador1, m.jugador1_id,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador2_id) as jugador2, m.jugador2_id,
				CASE
					WHEN m.jugador2_id IS NULL THEN (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.jugador1_id)
					WHEN m.ganador_id IS NULL THEN ''
					ELSE (SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.ganador_id)
				END as ganador, m.ganador_id
			FROM matches m
			WHERE m.category = " . $this->db->escape($category_id) . "
			AND m.gender = " . $this->db->escape($gender) . "
			ORDER BY m.ronda ASC, m.bracket_pos ASC, m.id ASC";
		$q = $this->db->query($sql);
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
		// Convertir fecha/hora vacías a NULL
		if(isset($data['fecha']) && ($data['fecha'] === '' || $data['fecha'] === '0000-00-00')) {
			$data['fecha'] = NULL;
		}
		if(isset($data['hora']) && $data['hora'] === '') {
			$data['hora'] = NULL;
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
		$sql = "SELECT m.id, m.ronda, m.score, m.gender, m.category, m.deadline, m.fecha, m.hora,
				c.name as categoria,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador1_id) as jugador1, m.jugador1_id,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador2_id) as jugador2, m.jugador2_id,
				CASE WHEN (SELECT COUNT(*) FROM reservations_partners WHERE reservation_id = m.jugador1_id AND partner_id = ?) > 0 THEN
					(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.jugador1_id)
					ELSE
					(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.jugador2_id)
				END as yo,
				CASE WHEN (SELECT COUNT(*) FROM reservations_partners WHERE reservation_id = m.jugador1_id AND partner_id = ?) > 0 THEN
					(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.jugador2_id)
					ELSE
					(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
						FROM reservations_partners rp
						JOIN partners p ON p.id = rp.partner_id
						WHERE rp.reservation_id = m.jugador1_id)
				END as rival,
				CASE WHEN (SELECT COUNT(*) FROM reservations_partners WHERE reservation_id = m.jugador1_id AND partner_id = ?) > 0 THEN m.jugador1_id ELSE m.jugador2_id END as yo_id,
				NULL as rival_wa
				FROM matches m
				JOIN category c ON c.id = m.category
				WHERE (
					(SELECT COUNT(*) FROM reservations_partners WHERE reservation_id = m.jugador1_id AND partner_id = ?) > 0
					OR
					(SELECT COUNT(*) FROM reservations_partners WHERE reservation_id = m.jugador2_id AND partner_id = ?)
				)
				AND m.ganador_id IS NULL
				AND m.jugador2_id IS NOT NULL
				ORDER BY CASE WHEN c.name LIKE '%2nd chance%' THEN 0 ELSE 1 END ASC, m.id ASC";
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
			->select('m.id, m.jugador1_id, m.jugador2_id, m.ganador_id, m.ronda, m.category, m.gender, m.bracket_pos, m.score')
			->from('matches m')
			->where('m.category', $category_id)
			->where('m.gender', $gender)
			->where('m.ronda', $ronda)
			->order_by('m.bracket_pos ASC')
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

	// Obtener standings de un grupo
	public function getGroupStandings($category_id, $gender, $ronda) {
		// Obtener todos los jugadores únicos del grupo
		$sql = "SELECT DISTINCT
				CASE
					WHEN jugador1_id IS NOT NULL THEN jugador1_id
					WHEN jugador2_id IS NOT NULL THEN jugador2_id
				END as reservation_id
			FROM matches
			WHERE category = $category_id AND gender = '$gender' AND ronda = '$ronda'
			AND (jugador1_id IS NOT NULL OR jugador2_id IS NOT NULL)";

		$jugadores_q = $this->db->query($sql)->result();
		$standings = array();

		foreach($jugadores_q as $j) {
			$res_id = $j->reservation_id;

			// Obtener nombre del jugador
			$nombre_q = $this->db->query(
				"SELECT GROUP_CONCAT(p.name SEPARATOR ' / ') as nombre
				 FROM reservations_partners rp
				 JOIN partners p ON p.id = rp.partner_id
				 WHERE rp.reservation_id = $res_id"
			)->row();
			$nombre = $nombre_q ? $nombre_q->nombre : '?';

			// Contar partidos jugados
			$pj_q = $this->db->query(
				"SELECT COUNT(*) as pj FROM matches
				 WHERE category = $category_id AND gender = '$gender' AND ronda = '$ronda'
				 AND ganador_id IS NOT NULL
				 AND (jugador1_id = $res_id OR jugador2_id = $res_id)"
			)->row();
			$pj = $pj_q->pj;

			// Contar partidos ganados
			$pg_q = $this->db->query(
				"SELECT COUNT(*) as pg FROM matches
				 WHERE category = $category_id AND gender = '$gender' AND ronda = '$ronda'
				 AND ganador_id = $res_id"
			)->row();
			$pg = $pg_q->pg;

			// Partidos perdidos
			$pp = $pj - $pg;

			// Puntos (3 por victoria, 0 por derrota)
			$pts = $pg * 3;

			$standings[] = array(
				'reservation_id' => $res_id,
				'nombre' => $nombre,
				'pj' => $pj,
				'pg' => $pg,
				'pp' => $pp,
				'dg' => 0, // Por ahora 0, se puede mejorar con scores
				'pts' => $pts
			);
		}

		// Ordenar por puntos desc, luego por PG desc
		usort($standings, function($a, $b) {
			if($a['pts'] !== $b['pts']) return $b['pts'] - $a['pts'];
			return $b['pg'] - $a['pg'];
		});

		return $standings;
	}
}
