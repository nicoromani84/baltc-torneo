<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Reservation');
	}

	public function index()	{
		$this->protect->setRequest('GET');
		date_default_timezone_set('America/Argentina/Buenos_Aires');

		if ( $this->User->isLogged() ) {
			$d['titulo'] 	= 'Torneo';
			$d['token']		= $this->protect->eToken();
			$d['categories'] = $this->Reservation->getCategories($this->session->gender);
			$d['partners'] = $this->User->getAllExceptMe($this->session->gender);
			$d['user'] = $this->session;
			$d['classname'] = 'reserva';

			// Si no hay horarios disponibles para hoy, saco el option
			$this->load->view('web/header',$d);
			$this->load->view('web/reserva');
			$this->load->view('web/footer');
		} else {
			$d['titulo'] 	= 'Login';
			$d['token']		= $this->protect->eToken();
			$d['classname'] = 'login';
			echo "La inscripción finalizó, muchas gracias";
			//$this->load->view('web/header',$d);
			//$this->load->view('web/login');
			//$this->load->view('web/footer');
		}
	}

	public function logout() {
		$this->protect->setRequest('GET');
		$this->session->sess_destroy();
		redirect(base_url());
	}

	public function validate() {
		$check = $this->User->check(
			$this->input->get('dni', true)
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
		$u = $this->User->check(
			$this->input->post('dni', true)
		);
		//si no existe
		if (!$u) {
			//Respuesta
			$res['action'] 	= false;
			$res['msg'] = 'El DNI ingresado no es válido.';
		}
		//si existe
		else {
			//Metemos el usuario en el array de session
			$s = array(
				'isLogged' 	=> true,
				'id'		=> $u->id,
				'name'		=> $u->name,
				'dni'		=> $u->dni,
				'email'		=> $u->email,
				'gender'	=> $u->gender
			);
			$this->session->set_userdata($s);
			$res['action'] 	= true;
		}
		//Retornamos Respuesta
		$this->protect->ajaxDie($res);
	}

	public function checkLogged() {
		$this->protect->ajaxDie($this->User->isLogged());
	}
}
