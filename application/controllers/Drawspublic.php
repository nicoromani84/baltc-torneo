<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drawspublic extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Partido_model');
		$this->load->model('Reservation');
	}

	public function index() {
		// Obtener todos los matches sorteados agrupados por category
		$sql = "SELECT DISTINCT m.category, m.gender, c.name as categoria
				FROM matches m
				JOIN category c ON c.id = m.category
				WHERE c.active = 1
				ORDER BY m.category, m.gender";
		$draws = $this->db->query($sql)->result();

		$d['titulo'] = 'Draws';
		$d['draws'] = $draws;
		$this->load->view('web/header', $d);
		$this->load->view('web/drawspublic', $d);
		$this->load->view('web/footer', $d);
	}

	public function getData() {
		$category = intval($this->input->get('cat'));
		$gender = $this->input->get('gen', true);

		$partidos = $this->Partido_model->getByCategoryAndGender($category, $gender);
		$q = $this->db->where('category', $category)->where('gender', $gender)->order_by('numero ASC')->get('sembrados');
		$sembrados = array();
		if($q->num_rows() > 0) {
			foreach($q->result() as $s) {
				$sembrados[$s->partner_id] = $s->numero;
			}
		}

		echo json_encode(array(
			'partidos' => $partidos ?: array(),
			'sembrados' => $sembrados
		));
	}
}
