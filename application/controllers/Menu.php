<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Reservation');
		$this->load->model('Partido_model');
	}

	private function requireLogin() {
		if ( !$this->User->isLogged() ) {
			redirect(base_url());
		}
	}

	private function baseData($titulo) {
		$this->requireLogin();
		return array(
			'titulo'    => $titulo,
			'token'     => $this->protect->eToken(),
			'user'      => $this->session,
			'classname' => 'reserva',
		);
	}

	public function index() {
		$this->protect->setRequest('GET');
		$d = $this->baseData('Torneo');
		$this->load->view('web/header', $d);
		$this->load->view('web/menu');
		$this->load->view('web/footer');
	}

	public function resultados() {
		$this->protect->setRequest('GET');
		$d = $this->baseData('Resultados');

		$user_id = $this->session->userdata('id');
		$user_gender = $this->session->userdata('gender');

		// Buscar en qué categoría juega el usuario
		$user_category = $this->Reservation->getCategoryByPlayer($user_id);

		$d['partidos'] = $this->Partido_model->getAllWithGender();
		$d['mi_category']   = $user_category;
		$d['mi_gender']     = $user_gender;
		$d['mi_partner_id'] = $user_id;

		$this->load->view('web/header', $d);
		$this->load->view('web/resultados');
		$this->load->view('web/footer');
	}

	public function programacion() {
		$this->protect->setRequest('GET');
		$d = $this->baseData('Programación');
		$d['partidos'] = $this->Partido_model->getAllWithGender();
		$this->load->view('web/header', $d);
		$this->load->view('web/programacion');
		$this->load->view('web/footer');
	}

	public function draws() {
		$this->protect->setRequest('GET');
		$d = $this->baseData('Draws');
		$d['categories'] = $this->Reservation->getCategories();
		$uid = $this->session->userdata('id');
		$d['mi_category'] = $uid ? $this->Reservation->getCategoryByPlayer($uid) : null;
		$d['mi_gender']   = $this->session->userdata('gender');
		// DEBUG - quitar despues
		error_log('DRAWS DEBUG - uid: ' . $uid . ' cat: ' . $d['mi_category'] . ' gen: ' . $d['mi_gender']);
		$this->load->view('web/header', $d);
		$this->load->view('web/draws');
		$this->load->view('web/footer');
	}
}
