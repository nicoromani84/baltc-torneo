<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Reservation');
		$this->load->model('Administrator');
	}

	public function index()	{
		$this->protect->setRequest('GET');
		if ( $this->Administrator->isLogged() ) {
			$this->_buildDash();
		} else {
			$this->_buildLogin();
		}
	}

	private function _buildLogin() {
		$d['titulo'] 	= 'Admin Login';
		$d['token']		= $this->protect->eToken();
		$d['section']	= 'admin-login';
		$this->load->view('admin/header',$d);
		$this->load->view('admin/login');
		$this->load->view('admin/footer');
	}

	private function _buildDash() {
		$d['titulo'] 	= 'Admin Dashboard';
		$d['token']		= $this->protect->eToken();
		$d['section']	= 'admin-dashboard';
		
		$reservations = $this->Administrator->getReservations('M', 1);
		$partners = $this->User->getAll();
		
		foreach($reservations as $reservation) {
			$reservation->partners = json_decode($reservation->partners);
		}

		$d['reservations'] = $reservations;

		$this->load->view('admin/header',$d);
		$this->load->view('admin/dashboard');
		$this->load->view('admin/footer');
	}

	public function logout() {
		$this->protect->setRequest('GET');
		$this->session->sess_destroy();
		redirect(base_url('/admin'));
	}

	public function validate() {
		$check = $this->Administrator->check(
			$this->input->get('username', true),
			$this->input->get('password', true)
		);
		$this->protect->ajaxDie($check ? true : false);
	}

	public function proccess() {
		//seteamos solo ajax
		$this->protect->setAjax();
		//seteamos el metodo obligatorio
		$this->protect->setRequest('POST');
		//Chequeamos el token
		//$this->protect->ajaxTokenCheck();
		//Si todo está en orden proseguimos verificando el user
		$u = $this->Administrator->check(
			$this->input->post('username', true),
			$this->input->post('password', true)
		);
		//si no existe
		if (!$u) {
			//Respuesta
			$res['action'] 	= false;
			$res['msg'] = 'El usuario ingresado no es válido.';
		}
		//si existe
		else {
			//Metemos el usuario en el array de session
			$s = array(
				'admin' =>
					array(
						'isLogged' 	=> true,
						'id'				=> $u->id,
						'name'				=> $u->username
					)
			);
			$this->session->set_userdata($s);
			$res['action'] 	= true;
		}
		//Retornamos Respuesta
		$this->protect->ajaxDie($res);
	}

	public function checkLogged() {
		$this->protect->ajaxDie($this->Administrator->isLogged());
	}

	public function getCategories() {
		//seteamos solo ajax
		$this->protect->setAjax();
		//seteamos el metodo obligatorio
		$this->protect->setRequest('POST');
		$gender = $this->input->post('gender');
		$categories = $this->Reservation->getCategories($gender);
		$res = array('action' => $categories !== false, 'categories' => $categories);
		$this->protect->ajaxDie($res);
	}

	public function getReservations() {
		//seteamos solo ajax
		$this->protect->setAjax();
		//seteamos el metodo obligatorio
		$this->protect->setRequest('POST');
		$gender = $this->input->post('gender', true);
		$category = $this->input->post('category', true);
		$reservations = $this->Administrator->getReservations($gender, $category);
		$partners = $this->User->getAll();
		
		foreach($reservations as $reservation) {
			$reservation->partners = json_decode($reservation->partners);
		}

		$res = array(
			'reservations' 		=> $reservations,
			'partners'			=> $partners
		);

		$this->protect->ajaxDie($res);
	}

	public function addReservation() {
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->protect->setRequest('POST');
		$post = $this->input->post();
		$response = array();

		// Valido los campos
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('category', 'Categoría', 'required')->set_message('required', 'Debe seleccionar una categoría.');
		if ($this->form_validation->run() == FALSE) {
			$response['action'] = false;
			$response['msg'] = validation_errors();
			$this->protect->ajaxDie($response);
		}

		for($i = 0; $i < count($post['partners']); $i++) {
			$post['partners'][$i] = $this->User->getById($post['partners'][$i]);
			$post['partners'][$i]->name = strtolower($post['partners'][$i]->name);
		}

		$email_data = array(
			'category' 		=> $post['category'],
			'partners' 		=> $post['partners']
		);

		// Enviamos confirmación por email
		$this->sendConfirmation($email_data);

		$partners_blocked = array(1026, 1027);

		// Guardamos la reserva
		$add = $this->Reservation->add($post);
		$addPartners = false;

		// Si se guardó la reserva, guardamos los participantes
		if(is_integer($add)) {
			$partners = array();
			for($i = 0; $i < count($post['partners']); $i++) {
				array_push($partners, array(
					'partner_id' 		=> intval($post['partners'][$i]->id),
					'reservation_id'	=> $add
				));
			}
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

	public function editReservation() {
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->protect->setRequest('POST');
		$post = $this->input->post();
		$response = array();

		// Valido los campos
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('date', 'Fecha', 'required')->set_message('required', 'Debe seleccionar una fecha.');
		$this->form_validation->set_rules('hour', 'Turno', 'required')->set_message('required', 'Debe seleccionar un turno.');
		$this->form_validation->set_rules('court', 'Cancha', 'required')->set_message('required', 'Debe seleccionar una cancha.');
		if ($this->form_validation->run() == FALSE) {
			$response['action'] = false;
			$response['msg'] = validation_errors();
			$this->protect->ajaxDie($response);
		}

		// Valido que no haya hecho una reserva en este día
		// $recurrent = $this->Reservation->canReserve($post['date'], array(intval($this->session->id), intval($post['partner'])));
		// if($recurrent) {
		// 	$response['action'] = false;
		// 	$response['msg'] = array();
		// 	for($i = 0; $i < count($recurrent); $i++) {
		// 		if(intval($recurrent[$i]->id) == $this->session->id) {
		// 			$response['msg'][$i] = 'Ya has realizado una reserva en esta fecha.';
		// 		} else {
		// 			$response['msg'][$i] = '<strong class="name">' . strtolower($recurrent[$i]->name) . '</strong> ya ha realizado una reserva en esta fecha.';
		// 		}
		// 	}
		// 	$this->protect->ajaxDie($response);
		// }

		// Formateo la fecha del turno
		$post['turn'] = date('Y-m-d H:i:s', strtotime($post['date'] . ' ' . $post['hour']));
		$court = $this->Reservation->getByID($post['court']);
		$user = $this->User->getById($this->session->id);

		for($i = 0; $i < count($post['partners']); $i++) {
			$post['partners'][$i] = $this->User->getById($post['partners'][$i]);
			$post['partners'][$i]->name = strtolower($post['partners'][$i]->name);
		}

		// Guardamos la reserva
		$updated = $this->Reservation->edit($post['id'], $post);
		$addPartners = false;

		// Si se guardó la reserva, guardamos los participantes
		$deleted = $this->Reservation->deleteReservationPartners($post['id']);
		$partners = array();
		for($i = 0; $i < count($post['partners']); $i++) {
			array_push($partners, array(
				'partner_id' 		=> intval($post['partners'][$i]->id),
				'reservation_id'	=> $post['id']
			));
		}
		$addPartners = $this->Reservation->addReservationPartners($partners);

		$response['action'] = $addPartners;
		$response['updated'] = $updated;
		$response['deleted'] = $deleted;
		$response['addPartners'] = $addPartners;
		$response['partners'] = $post['partners'];
		$this->protect->ajaxDie($response);
	}

	public function delete() {
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->protect->setRequest('POST');
		$post = $this->input->post();
		if(!$this->input->post('id', true))
			$this->protect->ajaxDie(array('action' => false, 'ID requerido.'));

		$this->protect->ajaxDie(array('action' => $this->Reservation->delete($post['id'])));
	}

	public function sendConfirmation($data) {
	    $this->load->library('email');

		$subject = 'Ya tenes tu inscripción confirmada';
		$datos = array();
		for($i = 0; $i < count($data['partners']); $i++) {
			if(filter_var($data['partners'][$i]->email, FILTER_VALIDATE_EMAIL) !== false) {
				for($e = 0; $e < count($data['partners']); $e++) {
					if($data['partners'][$i]->name !== $data['partners'][$e]->name) {
						if(!is_array($datos[$i]))
							$datos[$i] = $data;
						unset($datos[$i]['partners']);
						$datos[$i]['user'] = $data['partners'][$i];
						if(!is_array($datos[$i]['p']))
							$datos[$i]['p'] = array();
						array_push($datos[$i]['p'], $data['partners'][$e]);
					}
				}
			}
		}

		for($i = 0; $i < count($datos); $i++) {
			$d = array_map(function($v){
				return str_replace(',', '', $v->name);
			}, $datos[$i]['p']);
			for($e = 0; $e < count($d); $e++) {
				if($e == 0) {
					$name = ucwords($d[$e], ' ');
				} else {
					if($e < count($d) - 1) {
						$name = $name . ', ' . ucwords($d[$e], ' ');
					} else {
						$name = $name . ' y ' . ucwords($d[$e], ' ');
					}
					
				}
			}
			$datos[$i]['partner'] = $name;
			$datos[$i]['nombre'] = ucwords(str_replace(',', '', $datos[$i]['user']->name), ' ');

			$body = $this->load->view('email/reservation_confirm.php', $datos[$i], true);
			$result = $this->email
			    ->from('secretaria@baltc.net', 'Secretaría BALTC')
			    ->to($datos[$i]['user']->email)
			    ->subject($subject)
			    ->message($body)
			    ->send();
		}

		return $datos;
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
