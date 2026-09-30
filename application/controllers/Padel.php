<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Padel extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
	}

	public function index()	{
		$this->protect->setRequest('GET');
		if ( !$this->User->isLogged() ) {
			redirect(base_url());
		}

		date_default_timezone_set('America/Argentina/Buenos_Aires');

		// Verificar si ya está inscripto en pádel
		$userRegistered = $this->db->where('user_id', $this->session->userdata('id'))->get('padel_reservations')->num_rows() > 0;

		if ($userRegistered) {
			redirect(base_url('padel/inscripto'));
		}

		$d['titulo'] 	= 'Pádel';
		$d['token']		= $this->protect->eToken();
		$d['categories'] = array((object)array('id' => 1, 'name' => 'Caballeros', 'gender' => 'M'), (object)array('id' => 2, 'name' => 'Damas', 'gender' => 'F'));
		$d['partners'] = $this->User->getAllExceptMe($this->session->gender);
		$d['user'] = $this->session;

		$this->load->view('web/header',$d);
		$this->load->view('web/padel/reserva');
		$this->load->view('web/footer');
	}

	public function inscripto() {
		$this->protect->setRequest('GET');
		if ( !$this->User->isLogged() ) {
			redirect(base_url());
		}

		date_default_timezone_set('America/Argentina/Buenos_Aires');

		// Obtener inscripción actual
		$d['titulo'] = 'Ya estás inscripto en Pádel';
		$d['user'] = $this->session;

		$inscripcion = $this->db->where('user_id', $this->session->userdata('id'))->get('padel_reservations')->row();

		if ($inscripcion) {
			$partner = $this->User->getById($inscripcion->partner_id);
			$d['partner_name'] = $partner ? $partner->name : 'Partner';
			$d['categoria'] = ($inscripcion->gender === 'M') ? 'Caballeros' : 'Damas';
			$d['inscripcion'] = $inscripcion;
		} else {
			// Si no está inscripto, redirigir a inscripción
			redirect(base_url('padel'));
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

		// Validar campos requeridos
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('partner', 'Compañero', 'required')->set_message('required', 'Debe seleccionar un compañero.');
		$this->form_validation->set_rules('category', 'Categoría', 'required')->set_message('required', 'Debe seleccionar una categoría.');
		if ($this->form_validation->run() == FALSE) {
			$response['action'] = false;
			$response['msg'] = validation_errors();
			$this->protect->ajaxDie($response);
		}

		// Validar que el partner existe y tiene el género correcto
		$partner = $this->User->getById(intval($post['partner']));
		if(!$partner) {
			$response['action'] = false;
			$response['msg'] = 'El compañero seleccionado no existe.';
			$this->protect->ajaxDie($response);
		}

		// Para pádel, solo mismo género
		if($partner->gender != $this->session->gender) {
			$response['action'] = false;
			$response['msg'] = 'El compañero seleccionado no existe o no es del mismo género.';
			$this->protect->ajaxDie($response);
		}

		// Validar que no sea la misma persona
		if(intval($post['partner']) == intval($this->session->userdata('id'))) {
			$response['action'] = false;
			$response['msg'] = 'No puedes seleccionarte a ti mismo como compañero.';
			$this->protect->ajaxDie($response);
		}

		// Obtener datos del usuario
		$user = $this->User->getById($this->session->userdata('id'));

		// Validar que ninguno de los dos esté ya inscripto en pádel
		$userRegistered = $this->db->where('user_id', $this->session->userdata('id'))->get('padel_reservations')->num_rows() > 0;
		$partnerRegistered = $this->db->where('user_id', intval($post['partner']))->get('padel_reservations')->num_rows() > 0;

		if($userRegistered || $partnerRegistered) {
			$response['action'] = false;
			if($userRegistered && $partnerRegistered) {
				$response['msg'] = 'Ambos jugadores ya están inscriptos en pádel.';
			} else if($userRegistered) {
				$response['msg'] = 'Ya estás inscripto en pádel.';
			} else {
				$response['msg'] = $partner->name . ' ya está inscripto en pádel.';
			}
			$this->protect->ajaxDie($response);
		}

		// Guardar la reserva en padel_reservations
		$data = array(
			'user_id' => intval($this->session->userdata('id')),
			'partner_id' => intval($post['partner']),
			'gender' => $this->session->gender,
			'created_at' => date('Y-m-d H:i:s')
		);

		if ($this->db->insert('padel_reservations', $data)) {
			$response['action'] = true;
		} else {
			$response['action'] = false;
			$response['msg'] = 'Error al grabar la inscripción';
		}

		$this->protect->ajaxDie($response);
	}

	public function dashboard() {
		$this->protect->setRequest('GET');
		if ( !$this->User->isLogged() ) {
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
