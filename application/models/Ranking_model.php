<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ranking_model extends CI_Model {

	private function getPuntosRonda($ronda, $maxRonda) {
		if($maxRonda <= 1) return 10;
		if($ronda == $maxRonda) return 80;
		if($ronda >= $maxRonda - 1) return 40;
		if($ronda >= ceil($maxRonda * 0.75)) return 20;
		if($ronda >= ceil($maxRonda * 0.5)) return 10;
		return 5;
	}

	private function getMultiplicadorCategoria($categoria) {
		if(preg_match('/(1|primera|1ra)/i', $categoria)) return 3;
		if(preg_match('/(2|segunda|2da)/i', $categoria)) return 2;
		if(preg_match('/(3|tercera|3era)/i', $categoria)) return 1;
		return 1;
	}

	public function getRankingByGender($gender) {
		$sql = "SELECT
				m.ganador_id,
				m.category,
				m.ronda,
				p.name,
				p.gender,
				c.name as categoria
			FROM matches m
			JOIN partners p ON p.id = m.ganador_id
			JOIN category c ON c.id = m.category
			WHERE p.gender = ?
			AND m.ganador_id IS NOT NULL
			AND c.name NOT LIKE '%2nd chance%'
			ORDER BY m.category, m.ronda";

		$q = $this->db->query($sql, array($gender));
		if($q->num_rows() === 0) {
			return array();
		}

		$matches = $q->result();

		$maxRondas = array();
		$puntajes = array();

		foreach($matches as $match) {
			$key = $match->category;
			if(!isset($maxRondas[$key])) {
				$maxRondas[$key] = 0;
			}
			$maxRondas[$key] = max($maxRondas[$key], $match->ronda);
		}

		foreach($matches as $match) {
			$playerId = $match->ganador_id;
			$maxRonda = $maxRondas[$match->category];
			$puntosRonda = $this->getPuntosRonda($match->ronda, $maxRonda);
			$multiplicador = $this->getMultiplicadorCategoria($match->categoria);
			$puntos = $puntosRonda * $multiplicador;

			if(!isset($puntajes[$playerId])) {
				$puntajes[$playerId] = array(
					'id' => $playerId,
					'name' => $match->name,
					'gender' => $match->gender,
					'puntos' => 0,
					'victorias' => 0
				);
			}
			$puntajes[$playerId]['puntos'] += $puntos;
			$puntajes[$playerId]['victorias'] += 1;
		}

		usort($puntajes, function($a, $b) {
			$cmp = $b['puntos'] - $a['puntos'];
			return $cmp != 0 ? $cmp : $b['victorias'] - $a['victorias'];
		});

		return array_values($puntajes);
	}

	public function getRankingDetailedByGender($gender) {
		$sql = "SELECT
				m.ganador_id,
				m.category,
				m.ronda,
				p.name,
				p.gender,
				c.id as cat_id,
				c.name as categoria
			FROM matches m
			JOIN partners p ON p.id = m.ganador_id
			JOIN category c ON c.id = m.category
			WHERE p.gender = ?
			AND m.ganador_id IS NOT NULL
			AND c.name NOT LIKE '%2nd chance%'
			ORDER BY p.name, m.category, m.ronda";

		$q = $this->db->query($sql, array($gender));
		if($q->num_rows() === 0) {
			return array();
		}

		$matches = $q->result();

		$maxRondas = array();
		foreach($matches as $match) {
			$key = $match->category;
			if(!isset($maxRondas[$key])) {
				$maxRondas[$key] = 0;
			}
			$maxRondas[$key] = max($maxRondas[$key], $match->ronda);
		}

		$detailed = array();
		foreach($matches as $match) {
			$playerId = $match->ganador_id;
			$catId = $match->cat_id;
			$key = $playerId . '_' . $catId;

			$maxRonda = $maxRondas[$match->category];
			$puntosRonda = $this->getPuntosRonda($match->ronda, $maxRonda);
			$multiplicador = $this->getMultiplicadorCategoria($match->categoria);
			$puntos = $puntosRonda * $multiplicador;

			if(!isset($detailed[$key])) {
				$detailed[$key] = (object)array(
					'id' => $playerId,
					'name' => $match->name,
					'gender' => $match->gender,
					'cat_id' => $catId,
					'categoria' => $match->categoria,
					'puntos_categoria' => 0,
					'victorias_categoria' => 0
				);
			}
			$detailed[$key]->puntos_categoria += $puntos;
			$detailed[$key]->victorias_categoria += 1;
		}

		return array_values($detailed);
	}
}
