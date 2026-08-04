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
		$d['categories'] = $this->Reservation->getCategories();
		$d['mi_category'] = $this->Reservation->getCategoryByPlayer($this->session->userdata('id'));
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

		$sql = "SELECT DISTINCT c.id as category, g.gender
				FROM category c
				CROSS JOIN (SELECT 'M' as gender UNION SELECT 'F') g
				WHERE c.active = 1
				ORDER BY c.id ASC, g.gender ASC";
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
		$this->protect->ajaxDie(array('action' => !empty($partidos), 'partidos' => $partidos ?: array(), 'sembrados' => $sembrados, 'debug' => array('category' => $category, 'gender' => $gender, 'partidos_count' => count($partidos))));
	}
}
