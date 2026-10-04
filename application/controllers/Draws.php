<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Draws extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Partido_model');
		$this->load->model('Reservation');
	}

	public function index() {
		$public = $this->input->get('public');
		if($public === 'html') {
			$d['titulo'] = 'Draws';
			$d['categories'] = $this->Reservation->getCategories();
			$d['mi_category'] = '';
			$d['mi_gender'] = '';
			$d['mi_partner_id'] = '';
			$this->load->view('web/header', $d);
			$this->load->view('web/draws_public', $d);
			$this->load->view('web/footer', $d);
			return;
		}
		if($public) {
			$q = $this->db->query("SELECT DISTINCT category, gender FROM matches ORDER BY category, gender");
			$draws = $q->num_rows() > 0 ? $q->result() : array();
			header('Content-Type: application/json');
			echo json_encode(['draws' => $draws, 'count' => count($draws)]);
			exit();
		}

		$this->protect->setRequest('GET');
		if(!$this->User->isLogged()) redirect(base_url());
		$d['titulo']     = 'Draws';
		$d['token']      = $this->protect->eToken();
		$d['user']       = $this->session;
		$d['classname']  = 'reserva';

		$d['categories'] = $this->Reservation->getCategories();
		$d['mi_category'] = $this->Reservation->getCategoryByPlayer($this->session->userdata('id'), 'doubles');
		$d['mi_gender']   = $this->session->userdata('gender');
		$d['mi_partner_id'] = $this->session->userdata('id');
		$this->load->view('web/header', $d);
		$this->load->view('web/draws');
		$this->load->view('web/footer');
	}

	public function test() {
		// Debug endpoint
		header('Content-Type: application/json');

		$matchCount = $this->db->count_all('matches');
		$catCount = $this->db->count_all('category');
		$q = $this->db->get('matches');

		echo json_encode(array(
			'test' => 'ok',
			'match_count' => $matchCount,
			'category_count' => $catCount,
			'db_connected' => $matchCount >= 0,
			'first_match' => $q->row_array()
		));
		exit();
	}

	public function getDrawsDisponibles() {
		// Get all available draws for tab display
		header('Content-Type: application/json');

		try {
			$sql = "SELECT DISTINCT m.category, m.gender, c.name as categoria
					FROM matches m
					JOIN category c ON c.id = m.category
					WHERE c.active = 1
					ORDER BY m.category ASC, m.gender ASC";
			$q = $this->db->query($sql);

			if(!$q) {
				echo json_encode(array('success' => false, 'error' => 'Query failed', 'db_error' => $this->db->error()));
				exit();
			}

			$draws = array();
			if($q->num_rows() > 0) {
				foreach($q->result() as $row) {
					$draws[] = array(
						'category' => intval($row->category),
						'gender' => $row->gender,
						'categoria' => $row->categoria
					);
				}
			}

			echo json_encode(array('success' => true, 'draws' => $draws, 'count' => count($draws)));
		} catch(Exception $e) {
			echo json_encode(array('success' => false, 'error' => $e->getMessage()));
		}
		exit();
	}

	public function getDrawsDisponibles_old() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->User->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$matchCount = $this->db->count_all('matches');
		$catCount = $this->db->count_all('category');

		$this->protect->ajaxDie(array(
			'action'=>true,
			'draws'=> array(),
			'debug_matches' => $matchCount,
			'debug_cats' => $catCount
		));
	}

	public function getData() {
		// Allow both authenticated and public access
		$category = intval($this->input->post('category'));
		$gender   = $this->input->post('gender', true);

		if(!$category || !$gender) {
			header('Content-Type: application/json');
			echo json_encode(['action' => false, 'partidos' => [], 'sembrados' => []]);
			exit();
		}

		$partidos = $this->Partido_model->getByCategoryAndGender($category, $gender);
		$q = $this->db->where('category', $category)->where('gender', $gender)->order_by('numero ASC')->get('sembrados');
		$sembrados = array();
		if($q->num_rows() > 0) {
			foreach($q->result() as $s) {
				$sembrados[$s->partner_id] = $s->numero;
			}
		}

		// Detectar si hay grupos
		$is_groups = false;
		$groups_data = array();
		$has_brackets = false;
		if(!empty($partidos)) {
			foreach($partidos as $p) {
				if(strpos($p->ronda, 'Grupo') === 0) {
					$is_groups = true;
				}
				if(in_array($p->ronda, ['Semifinal', 'Final', 'Cuartos de Final'])) {
					$has_brackets = true;
				}
			}
		}

		// Si hay grupos, obtener standings
		if($is_groups) {
			$grupos = array();
			$rondas_unicas = array();
			foreach($partidos as $p) {
				if(strpos($p->ronda, 'Grupo') === 0 && !in_array($p->ronda, $rondas_unicas)) {
					$rondas_unicas[] = $p->ronda;
				}
			}
			foreach($rondas_unicas as $ronda) {
				$standings = $this->Partido_model->getGroupStandings($category, $gender, $ronda);
				$grupos[$ronda] = $standings;
			}
			$groups_data = $grupos;
		}

		// Si hay brackets después de grupos, mostrar también los partidos de bracket
		if($has_brackets) {
			$is_groups = false; // Cambiar a modo bracket si hay brackets
		}

		$response = array(
			'action' => !empty($partidos),
			'partidos' => !empty($partidos) ? $partidos : array(),
			'sembrados' => $sembrados,
			'is_groups' => $is_groups,
			'groups' => $groups_data
		);

		header('Content-Type: application/json');
		echo json_encode($response);
		exit();
	}
}
