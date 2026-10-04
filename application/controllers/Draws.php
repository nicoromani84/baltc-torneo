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
		$this->protect->setRequest('GET');
		if(!$this->User->isLogged()) redirect(base_url());
		$d['titulo']     = 'Draws';
		$d['token']      = $this->protect->eToken();
		$d['user']       = $this->session;
		$d['classname']  = 'reserva';
		// Mostrar solo categorías que tienen partidos sorteados (draws)
		$sql = "SELECT DISTINCT c.id, c.name, c.gender, c.active
				FROM category c
				INNER JOIN matches m ON m.category = c.id
				WHERE c.active = 1 AND c.name NOT LIKE '%2nd chance%'
				ORDER BY c.id ASC";
		$q = $this->db->query($sql);
		$d['categories'] = $q->num_rows() > 0 ? $q->result() : array();
		$d['mi_category'] = $this->Reservation->getCategoryByPlayer($this->session->userdata('id'), 'doubles');
		$d['mi_gender']   = $this->session->userdata('gender');
		$d['mi_partner_id'] = $this->session->userdata('id');
		$this->load->view('web/header', $d);
		$this->load->view('web/draws');
		$this->load->view('web/footer');
	}

	public function getDrawsDisponibles() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->User->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		// Mostrar solo categorías que tienen partidos sorteados (tienen matches)
		$sql = "SELECT DISTINCT c.id as category, c.gender
				FROM category c
				INNER JOIN matches m ON m.category = c.id
				WHERE c.active = 1 AND c.name NOT LIKE '%2nd chance%'
				ORDER BY c.id ASC";
		$q = $this->db->query($sql);
		$this->protect->ajaxDie(array('action'=>true, 'draws'=> $q->num_rows() > 0 ? $q->result() : array()));
	}

	public function getData() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$category = intval($this->input->post('category'));
		$gender   = $this->input->post('gender', true);
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

		$this->protect->ajaxDie(array(
			'action' => !empty($partidos),
			'partidos' => $partidos ?: array(),
			'sembrados' => $sembrados,
			'is_groups' => $is_groups,
			'groups' => $groups_data,
			'debug' => array('category' => $category, 'gender' => $gender, 'partidos_count' => count($partidos))
		));
	}
}
