<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ranking_model extends CI_Model {

	// Obtener ranking general ponderado (1ra, 2da, 3era)
	public function getRankingGeneral() {
		$sql = "SELECT
				p.id,
				p.name,
				p.gender,
				SUM(CASE
					WHEN c.name LIKE '%1%ra%' OR c.name LIKE '1%' THEN 3
					WHEN c.name LIKE '%2%da%' OR c.name LIKE '2%' THEN 2
					WHEN c.name LIKE '%3%era%' OR c.name LIKE '3%' THEN 1
					ELSE 0
				END) AS puntos,
				COUNT(m.id) AS victorias
			FROM partners p
			JOIN matches m ON m.ganador_id = p.id
			JOIN category c ON c.id = m.category
			WHERE c.name NOT LIKE '%2nd chance%'
			GROUP BY p.id, p.name, p.gender
			ORDER BY p.gender ASC, puntos DESC, victorias DESC";

		$q = $this->db->query($sql);
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Obtener ranking por género
	public function getRankingByGender($gender) {
		$sql = "SELECT
				p.id,
				p.name,
				p.gender,
				SUM(CASE
					WHEN c.name LIKE '%1%ra%' OR c.name LIKE '1%' THEN 3
					WHEN c.name LIKE '%2%da%' OR c.name LIKE '2%' THEN 2
					WHEN c.name LIKE '%3%era%' OR c.name LIKE '3%' THEN 1
					ELSE 0
				END) AS puntos,
				COUNT(m.id) AS victorias
			FROM partners p
			JOIN matches m ON m.ganador_id = p.id
			JOIN category c ON c.id = m.category
			WHERE p.gender = ?
			AND c.name NOT LIKE '%2nd chance%'
			GROUP BY p.id, p.name, p.gender
			ORDER BY puntos DESC, victorias DESC";

		$q = $this->db->query($sql, array($gender));
		return ($q->num_rows() > 0) ? $q->result() : array();
	}

	// Desglose de puntos por categoría
	public function getRankingDetailedByGender($gender) {
		$sql = "SELECT
				p.id,
				p.name,
				p.gender,
				c.name AS categoria,
				CASE
					WHEN c.name LIKE '%1%ra%' OR c.name LIKE '1%' THEN 3
					WHEN c.name LIKE '%2%da%' OR c.name LIKE '2%' THEN 2
					WHEN c.name LIKE '%3%era%' OR c.name LIKE '3%' THEN 1
					ELSE 0
				END AS puntos_categoria,
				COUNT(m.id) AS victorias_categoria
			FROM partners p
			JOIN matches m ON m.ganador_id = p.id
			JOIN category c ON c.id = m.category
			WHERE p.gender = ?
			AND c.name NOT LIKE '%2nd chance%'
			GROUP BY p.id, p.name, p.gender, c.id, c.name
			ORDER BY p.name ASC,
				CASE
					WHEN c.name LIKE '%1%ra%' OR c.name LIKE '1%' THEN 0
					WHEN c.name LIKE '%2%da%' OR c.name LIKE '2%' THEN 1
					WHEN c.name LIKE '%3%era%' OR c.name LIKE '3%' THEN 2
					ELSE 3
				END ASC";

		$q = $this->db->query($sql, array($gender));
		return ($q->num_rows() > 0) ? $q->result() : array();
	}
}
