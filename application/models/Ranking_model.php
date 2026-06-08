<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ranking_model extends CI_Model {

	private function getRondaOrder($ronda) {
		if(preg_match('/final/i', $ronda)) return 4;
		if(preg_match('/semifinal/i', $ronda)) return 3;
		if(preg_match('/cuartos/i', $ronda)) return 2;
		if(preg_match('/2da.*ronda/i', $ronda)) return 1;
		if(preg_match('/1ra.*ronda/i', $ronda)) return 0;
		return -1;
	}

	private function getPuntosRonda($ronda, $maxRondaOrder) {
		$rondaOrder = $this->getRondaOrder($ronda);

		if($rondaOrder < 0) return 0;

		if($rondaOrder == $maxRondaOrder) return 80;
		if($rondaOrder == $maxRondaOrder - 1) return 40;
		if($rondaOrder == $maxRondaOrder - 2) return 20;

		if($rondaOrder < 2) return 3;

		return 0;
	}

	private function getMultiplicadorCategoria($categoria) {
		if(preg_match('/(1|primera|1ra)/i', $categoria)) return 3;
		if(preg_match('/(2|segunda|2da)/i', $categoria)) return 2;
		if(preg_match('/(3|tercera|3era)/i', $categoria)) return 1;
		return 1;
	}

	private function getPuntosBase($categoria) {
		if(preg_match('/(1|primera|1ra)/i', $categoria)) return 270;
		if(preg_match('/(2|segunda|2da)/i', $categoria)) return 150;
		if(preg_match('/(3|tercera|3era)/i', $categoria)) return 100;
		return 0;
	}

	private function getDivisorCategoria($categoria) {
		if(preg_match('/(1|primera|1ra)/i', $categoria)) return 1.0;
		if(preg_match('/(2|segunda|2da)/i', $categoria)) return 1.3;
		if(preg_match('/(3|tercera|3era)/i', $categoria)) return 4.8;
		return 1.0;
	}

	public function getRankingByGender($gender) {
		$sql = "SELECT DISTINCT
				p.id,
				p.name,
				p.gender,
				c.id as cat_id,
				c.name as categoria
			FROM reservations_partners rp
			JOIN reservations r ON r.id = rp.reservation_id
			JOIN partners p ON p.id = rp.partner_id
			JOIN category c ON c.id = r.category
			WHERE p.gender = ?
			AND c.name NOT LIKE '%2nd chance%'
			ORDER BY p.id";

		$q = $this->db->query($sql, array($gender));
		if($q->num_rows() === 0) {
			return array();
		}

		$inscriptos = $q->result();
		$puntajes = array();

		foreach($inscriptos as $insc) {
			$playerId = $insc->id;
			if(!isset($puntajes[$playerId])) {
				$puntosBase = $this->getPuntosBase($insc->categoria);
				$puntajes[$playerId] = array(
					'id' => $playerId,
					'name' => $insc->name,
					'gender' => $insc->gender,
					'categoria' => $insc->categoria,
					'cat_id' => $insc->cat_id,
					'puntos' => $puntosBase,
					'victorias' => 0
				);
			}
		}

		$sqlMatches = "SELECT
				m.ganador_id,
				m.category,
				m.ronda,
				c.id as cat_id,
				c.name as categoria
			FROM matches m
			JOIN category c ON c.id = m.category
			WHERE m.ganador_id IS NOT NULL
			AND c.name NOT LIKE '%2nd chance%'
			ORDER BY m.category, m.ronda";

		$q = $this->db->query($sqlMatches);
		if($q->num_rows() > 0) {
			$matches = $q->result();

			$maxRondas = array();
			foreach($matches as $match) {
				$key = $match->category;
				if(!isset($maxRondas[$key])) {
					$maxRondas[$key] = -1;
				}
				$rondaOrder = $this->getRondaOrder($match->ronda);
				$maxRondas[$key] = max($maxRondas[$key], $rondaOrder);
			}

			foreach($matches as $match) {
				$playerId = $match->ganador_id;
				if(isset($puntajes[$playerId])) {
					$maxRondaOrder = $maxRondas[$match->category];
					$puntosRonda = $this->getPuntosRonda($match->ronda, $maxRondaOrder);
					$multiplicador = $this->getMultiplicadorCategoria($match->categoria);
					$divisor = $this->getDivisorCategoria($match->categoria);
					$puntos = round(($puntosRonda * $multiplicador) / $divisor, 0);

					$puntajes[$playerId]['puntos'] += $puntos;
					$puntajes[$playerId]['victorias'] += 1;
				}
			}
		}

		usort($puntajes, function($a, $b) {
			$cmpPuntos = $b['puntos'] - $a['puntos'];
			if($cmpPuntos != 0) return $cmpPuntos;

			return $b['victorias'] - $a['victorias'];
		});

		return array_values($puntajes);
	}

	private function getOrdenCategoria($categoria) {
		if(preg_match('/(1|primera|1ra)/i', $categoria)) return 1;
		if(preg_match('/(2|segunda|2da)/i', $categoria)) return 2;
		if(preg_match('/(3|tercera|3era)/i', $categoria)) return 3;
		return 4;
	}

	public function getRankingDetailedByGender($gender) {
		$ranking = $this->getRankingByGender($gender);

		$sqlMatches = "SELECT
				m.ganador_id,
				m.category,
				m.ronda,
				c.id as cat_id,
				c.name as categoria
			FROM matches m
			JOIN category c ON c.id = m.category
			WHERE m.ganador_id IS NOT NULL
			AND c.name NOT LIKE '%2nd chance%'
			ORDER BY m.category, m.ronda";

		$q = $this->db->query($sqlMatches);
		$matches = $q->num_rows() > 0 ? $q->result() : array();

		$maxRondas = array();
		foreach($matches as $match) {
			$key = $match->category;
			if(!isset($maxRondas[$key])) {
				$maxRondas[$key] = -1;
			}
			$rondaOrder = $this->getRondaOrder($match->ronda);
			$maxRondas[$key] = max($maxRondas[$key], $rondaOrder);
		}

		$detailed = array();
		foreach($matches as $match) {
			$playerId = $match->ganador_id;
			$catId = $match->cat_id;
			$key = $playerId . '_' . $catId;

			$playerInfo = null;
			foreach($ranking as $r) {
				if($r['id'] == $playerId) {
					$playerInfo = $r;
					break;
				}
			}

			if($playerInfo) {
				$maxRondaOrder = $maxRondas[$match->category];
				$puntosRonda = $this->getPuntosRonda($match->ronda, $maxRondaOrder);
				$multiplicador = $this->getMultiplicadorCategoria($match->categoria);
				$divisor = $this->getDivisorCategoria($match->categoria);
				$puntos = round(($puntosRonda * $multiplicador) / $divisor, 0);

				if(!isset($detailed[$key])) {
					$detailed[$key] = (object)array(
						'id' => $playerId,
						'name' => $playerInfo['name'],
						'gender' => $playerInfo['gender'],
						'cat_id' => $catId,
						'categoria' => $match->categoria,
						'puntos_categoria' => 0,
						'victorias_categoria' => 0
					);
				}
				$detailed[$key]->puntos_categoria += $puntos;
				$detailed[$key]->victorias_categoria += 1;
			}
		}

		return array_values($detailed);
	}

	public function getDebugInfo() {
		$sql = "SELECT
				c.name as categoria,
				m.gender,
				MIN(m.ronda) as min_ronda,
				MAX(m.ronda) as max_ronda,
				COUNT(*) as total_matches,
				SUM(CASE WHEN m.ganador_id IS NOT NULL THEN 1 ELSE 0 END) as matches_con_ganador
			FROM matches m
			JOIN category c ON c.id = m.category
			WHERE c.name NOT LIKE '%2nd chance%'
			GROUP BY m.category, m.gender
			ORDER BY c.name, m.gender";

		$q = $this->db->query($sql);
		$categorias = $q->result();

		$debug = array(
			'categorias_info' => $categorias,
			'sample_matches_M' => array(),
			'sample_matches_F' => array()
		);

		foreach(array('M', 'F') as $gender) {
			$sql = "SELECT
					p.name,
					c.name as categoria,
					m.ronda,
					m.ganador_id
				FROM matches m
				JOIN partners p ON p.id = m.ganador_id
				JOIN category c ON c.id = m.category
				WHERE p.gender = ?
				AND m.ganador_id IS NOT NULL
				AND c.name NOT LIKE '%2nd chance%'
				ORDER BY m.ronda DESC
				LIMIT 5";
			$q = $this->db->query($sql, array($gender));
			$debug['sample_matches_' . $gender] = $q->result();
		}

		return $debug;
	}
}
