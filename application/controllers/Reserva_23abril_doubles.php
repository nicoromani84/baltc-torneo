<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reserva extends CI_Controller {

	public function __construct() {
		parent::__construct();

		//Unicamente usuarios loggeados
		//$this->protect->loggedUsers();

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

		// Si no hay horarios disponibles para hoy, saco el option
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

		// Valido los campos
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('partner', 'Oponente', 'required')->set_message('required', 'Debe seleccionar un oponente.');
		$this->form_validation->set_rules('category', 'Categoría', 'required')->set_message('required', 'Debe seleccionar una categoría.');
		if ($this->form_validation->run() == FALSE) {
			$response['action'] = false;
			$response['msg'] = validation_errors();
			$this->protect->ajaxDie($response);
		}

		/* Valido que no haya hecho una reserva en este día
		$recurrent = $this->Reservation->canReserve(array(intval($this->session->id), intval($post['partner'])));
		if($recurrent) {
			$response['action'] = false;
			$response['msg'] = array();
			for($i = 0; $i < count($recurrent); $i++) {
				if(intval($recurrent[$i]->id) == $this->session->id) {
					$response['msg'][$i] = 'Ya has participado.';
				} else {
					$response['msg'][$i] = '<strong class="name">' . strtolower($recurrent[$i]->name) . '</strong> ya ha participado.';
				}
			}
			$this->protect->ajaxDie($response);
		}*/

		// Formateo la fecha del turno
		$partner = $this->User->getById($post['partner']);
		$user = $this->User->getById($this->session->id);

		$email_data = array(
			'nombre' 		=> $this->session->name,
			'category'		=> $post['category'],
			'partner' 		=> $partner ? strtolower($partner->name) : ''
		);

		// Enviamos confirmación por email
		if(filter_var($user->email, FILTER_VALIDATE_EMAIL) !== false) {
			$this->sendConfirmation($email_data, $user->email);
		}
		/*
		if($court) {

			$email_owner_data = array(
				//'cuando' 		=> date('Y-m-d') == date('Y-m-d', strtotime($post['date'])) ? 'hoy' : 'mañana',
				'cuando'		=> 'el día ' . $this->translateDayName(date('l', strtotime($post['date']))) . ' ' . date('d/m', strtotime($post['date'])),
				'desde' 		=> $post['hour'],
				'hasta' 		=> date('H:i', strtotime($post['hour']) + 60*60),
				'cancha'		=> $court ? $court->name : '',
				'cancha_id'		=> $court ? $court->id : '',
				'club' 			=> $court ? $court->club : '',
				'partner' 		=> strtolower(ucwords($this->session->name)) . ', ' . ($partner ? strtolower($partner->name) : '')
			);

			if(filter_var($court->cemail, FILTER_VALIDATE_EMAIL) !== false) {
				$this->sendOwnerConfirmation($email_owner_data, $court->cemail);
			}

		}
		$response['email_owner_data'] = $court;
		*/

		// Guardamos la reserva
		$add = $this->Reservation->add($post);
		$addPartners = false;

		// Si se guardó la reserva, guardamos los participantes
		if(is_integer($add)) {
			$partners = array(
				array('partner_id' => intval($this->session->id), 'reservation_id' => $add),
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
