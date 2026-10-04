<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drawspublic extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Partido_model');
	}

	public function index() {
		$sql = "SELECT DISTINCT m.category, m.gender, c.name as categoria
				FROM matches m
				JOIN category c ON c.id = m.category
				WHERE c.active = 1
				ORDER BY m.category ASC, m.gender ASC";
		$q = $this->db->query($sql);
		$draws = $q->num_rows() > 0 ? $q->result() : array();

		header('Content-Type: application/json');
		echo json_encode(['draws' => $draws]);
	}
}
