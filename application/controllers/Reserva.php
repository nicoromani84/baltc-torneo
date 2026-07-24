<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reserva extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('User');
		$this->load->model('Reservation');
	}

	public function index()	{
		$this->protect->setRequest('GET');
		if ( !$this->User->isLogged() ) {
			redirect(base_url());
		}

		date_default_timezone_set('America/Argentina/Buenos_Aires');
		$d['titulo'] 	= 'Torneo';
		$d['token']		= $this->protect->eToken();
		$d['categories'] = $this->Reservation->getCategories($this->session->gender);
		$d['partners'] = $this->User->getAllExceptMe($this->session->gender);
		$d['user'] = $this->session;

		$this->load->view('web/header',$d);
		$this->load->view('web/reserva');
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

		// Validar que el partner existe y es del mismo género
		$partner = $this->User->getById(intval($post['partner']));
		if(!$partner || $partner->gender != $this->session->gender) {
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

		$cat_obj = $this->db->where('id', $post['category'])->get('category')->row();
		$cat_nombre = $cat_obj ? $cat_obj->name : $post['category'];
		$email_data = array(
			'nombre' 		=> $this->session->name,
			'category'		=> $cat_nombre,
			'partner' 		=> $partner->name
		);

		// Enviar confirmación por email
		if(filter_var($user->email, FILTER_VALIDATE_EMAIL) !== false) {
			$this->sendConfirmation($email_data, $user->email);
		}

		// Validar que ninguno de los dos esté ya inscripto en dobles
		$userRegistered = $this->Reservation->isPlayerRegistered($this->session->userdata('id'), 'doubles');
		$partnerRegistered = $this->Reservation->isPlayerRegistered(intval($post['partner']), 'doubles');

		if($userRegistered || $partnerRegistered) {
			$response['action'] = false;
			if($userRegistered && $partnerRegistered) {
				$response['msg'] = 'Ambos jugadores ya están inscriptos en dobles.';
			} else if($userRegistered) {
				$response['msg'] = 'Ya estás inscripto en dobles. Solo podés participar en una categoría.';
			} else {
				$response['msg'] = $partner->name . ' ya está inscripto en dobles. Solo cada jugador puede participar en una categoría.';
			}
			$this->protect->ajaxDie($response);
		}

		// Guardar la reserva
		$post['tournament_type'] = 'doubles';
		$add = $this->Reservation->add($post);
		$addPartners = false;

		// Si se guardó la reserva, guardar ambos participantes
		if(is_integer($add)) {
			$partners = array(
				array('partner_id' => intval($this->session->userdata('id')), 'reservation_id' => $add),
				array('partner_id' => intval($post['partner']), 'reservation_id' => $add)
			);
			$addPartners = $this->Reservation->addReservationPartners($partners);
		}

		$response['action'] = is_integer($add) && $addPartners;
		if(isset($add['code']))
			$response['error'] = $add;
		$this->protect->ajaxDie($response);
	}

	private function translateDayName($day) {
		return str_replace(
			array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
			array('lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'),
			$day
		);
	}

	public function sendConfirmation($data, $email) {
	    $this->load->library('email');

		$subject = 'Ya tenes tu inscripción confirmada';
		$body = $this->load->view('email/reservation_confirm.php', $data, true);
		$result = $this->email
		    ->from('secretaria@baltc.net', 'Secretaría BALTC')
		    ->to($email)
		    ->subject($subject)
		    ->message($body)
		    ->send();
	}

	private function sendOwnerConfirmation($data, $email) {
	    $this->load->library('email');

		$subject = 'Reserva desde el BALTC';
		$body = $this->load->view('email/reservation_court_owner_confirm.php', $data, true);
		$result = $this->email
		    ->from('secretaria@baltc.net', 'Secretaría BALTC')
		    ->to($email)
		    ->subject($subject)
		    ->message($body)
		    ->send();
	}
}
