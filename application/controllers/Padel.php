<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Padel extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Protect');
	}

	public function index() {
		$this->protect->setRequest('GET');
		if (!$this->User->isLogged()) {
			redirect(base_url());
		}

		date_default_timezone_set('America/Argentina/Buenos_Aires');
		$d['titulo'] = 'Pádel';
		$d['token'] = $this->protect->eToken();
		$d['user'] = $this->session;
		$d['category'] = ($this->session->gender === 'M') ? 'Caballeros' : 'Damas';

		$this->load->view('web/header', $d);
		$this->load->view('web/padel/reserva', $d);
		$this->load->view('web/footer');
	}

	public function inscripto() {
		$this->protect->setRequest('GET');
		if (!$this->User->isLogged()) {
			redirect(base_url());
		}

		date_default_timezone_set('America/Argentina/Buenos_Aires');
		$d['titulo'] = 'Inscripción Confirmada';
		$d['token'] = $this->protect->eToken();
		$d['user'] = $this->session;

		// Obtener última inscripción del usuario
		$query = $this->db->select('*')
			->from('padel_reservations')
			->where('user_id', $this->session->userdata('id'))
			->order_by('created_at', 'DESC')
			->limit(1)
			->get();

		if ($query->num_rows() > 0) {
			$reservation = $query->row();
			$partner = $this->User->getById($reservation->partner_id);
			$d['partner'] = $partner ? strtolower($partner->name) : 'N/A';
			$d['categoria'] = ($reservation->gender === 'M') ? 'Caballeros' : 'Damas';
		}

		$this->load->view('web/header', $d);
		$this->load->view('web/padel/inscripto', $d);
		$this->load->view('web/footer');
	}

	public function add() {
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->protect->setRequest('POST');
		$post = $this->input->post();
		$response = array();

		// Validar partner
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('partner', 'Compañero', 'required');
		if ($this->form_validation->run() == FALSE) {
			$response['action'] = false;
			$response['msg'] = validation_errors();
			$this->protect->ajaxDie($response);
		}

		// Validar que el partner existe
		$partner = $this->User->getById(intval($post['partner']));
		if (!$partner) {
			$response['action'] = false;
			$response['msg'] = 'El compañero seleccionado no existe.';
			$this->protect->ajaxDie($response);
		}

		// Validar género: debe ser del mismo género
		if ($partner->gender != $this->session->gender) {
			$response['action'] = false;
			$response['msg'] = 'El compañero debe ser del mismo género.';
			$this->protect->ajaxDie($response);
		}

		// No puedes inscribirse contigo mismo
		if (intval($post['partner']) == intval($this->session->userdata('id'))) {
			$response['action'] = false;
			$response['msg'] = 'No puedes seleccionarte a ti mismo como compañero.';
			$this->protect->ajaxDie($response);
		}

		// Verificar si ya está inscrito
		$existing = $this->db->where('user_id', $this->session->userdata('id'))->get('padel_reservations');
		if ($existing->num_rows() > 0) {
			$response['action'] = false;
			$response['msg'] = 'Ya estás inscripto en pádel.';
			$this->protect->ajaxDie($response);
		}

		// Grabar inscripción
		$data = array(
			'user_id' => $this->session->userdata('id'),
			'partner_id' => intval($post['partner']),
			'gender' => $this->session->gender,
			'created_at' => date('Y-m-d H:i:s')
		);

		if ($this->db->insert('padel_reservations', $data)) {
			$response['action'] = true;
			$response['msg'] = 'Inscripción confirmada';
		} else {
			$response['action'] = false;
			$response['msg'] = 'Error al grabar la inscripción';
		}

		$this->protect->ajaxDie($response);
	}

	public function getMismoGenero() {
		$this->protect->setRequest('GET');
		$response = array();

		$gender = ($this->session->gender === 'M') ? 'M' : 'F';

		// Obtener usuarios del mismo género excepto el usuario actual
		$query = $this->db->select('id, name')
			->from('players')
			->where('gender', $gender)
			->where('id !=', $this->session->userdata('id'))
			->where('status', 'active')
			->get();

		if ($query->num_rows() > 0) {
			$response['action'] = true;
			$response['data'] = $query->result();
		} else {
			$response['action'] = false;
			$response['data'] = array();
		}

		$this->protect->ajaxDie($response);
	}

	public function dashboard() {
		$this->protect->setRequest('GET');
		if (!$this->User->isLogged()) {
			redirect(base_url());
		}

		date_default_timezone_set('America/Argentina/Buenos_Aires');
		$d['titulo'] = 'Dashboard - Pádel';
		$d['token'] = $this->protect->eToken();
		$d['user'] = $this->session;

		// Estadísticas generales
		$caballeros = $this->db->where('gender', 'M')->get('padel_reservations')->num_rows();
		$damas = $this->db->where('gender', 'F')->get('padel_reservations')->num_rows();

		$d['total_inscriptos'] = $caballeros + $damas;
		$d['caballeros'] = $caballeros;
		$d['damas'] = $damas;

		// Listado de parejas
		$query = $this->db->select('pr.id, pr.gender, u1.name as player1, u2.name as player2, pr.created_at')
			->from('padel_reservations pr')
			->join('players u1', 'u1.id = pr.user_id', 'left')
			->join('players u2', 'u2.id = pr.partner_id', 'left')
			->order_by('pr.gender', 'DESC')
			->order_by('pr.created_at', 'DESC')
			->get();

		$d['parejas'] = $query->result();

		$this->load->view('web/header', $d);
		$this->load->view('web/padel/dashboard', $d);
		$this->load->view('web/footer');
	}
}
