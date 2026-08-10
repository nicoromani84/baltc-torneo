<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Reservation');
		$this->load->model('LoginLog');
	}

	public function index()	{
		$this->protect->setRequest('GET');
		date_default_timezone_set('America/Argentina/Buenos_Aires');

		if ( $this->User->isLogged() ) {
			$user_id = $this->session->userdata('id');
			$inscripto_doubles = $this->Reservation->isPlayerRegistered($user_id, 'doubles');

			// Verificar si las inscripciones están abiertas
			$q_setting = $this->db->where('key', 'inscripciones_abiertas')->get('settings');
			$inscripciones_abiertas = ($q_setting->num_rows() > 0 && $q_setting->row()->value == '1');

			// Si inscripciones cerradas → siempre al menu
			if(!$inscripciones_abiertas) {
				redirect(base_url('menu'));
			// Si ya inscripto en dobles → pantalla inscripto
			} elseif($inscripto_doubles) {
				$categoria = $this->Reservation->getCategoryByPlayer($user_id, 'doubles');
				$cat_nombre = $this->Reservation->getCategoryName($categoria);

				// Obtener el compañero del usuario
				$q_partner = $this->db
					->select('p.name')
					->from('reservations_partners rp')
					->join('reservations r', 'rp.reservation_id = r.id')
					->join('partners p', 'rp.partner_id = p.id')
					->where('r.tournament_type', 'doubles')
					->where('rp.partner_id !=', $user_id)
					->where('r.id IN (SELECT reservation_id FROM reservations_partners WHERE partner_id = ' . intval($user_id) . ')')
					->get();

				$partner_name = '';
				if($q_partner->num_rows() > 0) {
					$partner_name = $q_partner->row()->name;
				}

				$d['titulo'] 	= 'Inscripción';
				$d['token']		= $this->protect->eToken();
				$d['user'] 		= $this->session;
				$d['classname'] = 'reserva';
				$d['categoria'] = $cat_nombre;
				$d['partner'] = $partner_name;
				$this->load->view('web/header',$d);
				$this->load->view('web/inscripto');
				$this->load->view('web/footer');
			// Inscripciones abiertas y no inscripto en dobles → formulario
			} else {
				$d['titulo'] 	= 'Torneo';
				$d['token']		= $this->protect->eToken();
				$d['categories'] = $this->Reservation->getCategories($this->session->gender, true);
				$d['partners'] = $this->User->getAllExceptMe($this->session->gender);
				$d['user'] = $this->session;
				$d['classname'] = 'reserva';
				$this->load->view('web/header',$d);
				$this->load->view('web/reserva');
				$this->load->view('web/footer');
			}
		} else {
			// Verificar si las inscripciones están cerradas
			$q_setting = $this->db->where('key', 'inscripciones_abiertas')->get('settings');
			$inscripciones_abiertas = ($q_setting->num_rows() > 0 && $q_setting->row()->value == '1');

			$d['titulo'] 	= 'Interno de Dobles ';
			$d['token']		= $this->protect->eToken();
			$d['classname'] = 'login';
			$d['inscripciones_cerradas'] = !$inscripciones_abiertas;
			$this->load->view('web/header',$d);
			$this->load->view('web/login');
			$this->load->view('web/footer');
		}
	}

	public function logout() {
		$this->protect->setRequest('GET');
		$user_id = $this->session->userdata('id');
		if($user_id) {
			$this->LoginLog->logLogout($user_id);
		}
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
			//Registrar login en logs
			$this->LoginLog->logLogin($u->id, $u->dni, $u->name);
			$res['action'] 	= true;
		}
		//Retornamos Respuesta
		$this->protect->ajaxDie($res);
	}

	public function checkLogged() {
		$this->protect->ajaxDie($this->User->isLogged());
	}
}
