<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Reservation');
		$this->load->model('Administrator');
		$this->load->model('Partido_model');
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
		$d['jugadores']  = $this->Administrator->getJugadores();
		$d['reservations'] = array();
		$d['categories'] = $this->Administrator->getAllCategories();
		$q_s = $this->db->where('key', 'inscripciones_abiertas')->get('settings');
		$d['inscripciones_abiertas'] = ($q_s->num_rows() > 0 && $q_s->row()->value == '1');
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
// EMAILS DESHABILITADOS // 		$this->sendConfirmation($email_data);

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

	private function _sendProgramacionEmail($partido_id, $fecha, $hora) {
		$partido = $this->Partido_model->getById($partido_id);
		if(!$partido) return;

		$j1 = $this->User->getById($partido->jugador1_id);
		$j2 = $this->User->getById($partido->jugador2_id);
		if(!$j1 || !$j2) return;

		$fecha_formateada = date('l d \d\e F \d\e Y', strtotime($fecha));
		$dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado','Sunday'=>'Domingo');
		$meses = array('January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril','May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto','September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre');
		foreach($dias as $en=>$es) $fecha_formateada = str_replace($en, $es, $fecha_formateada);
		foreach($meses as $en=>$es) $fecha_formateada = str_replace($en, $es, $fecha_formateada);
		$hora_fmt = $hora ? substr($hora, 0, 5) . 'hs' : '';

		$jugadores = array(
			array('player' => $j1, 'rival' => $j2->name),
			array('player' => $j2, 'rival' => $j1->name),
		);

		foreach($jugadores as $item) {
			if(!filter_var($item['player']->email, FILTER_VALIDATE_EMAIL)) continue;
			$data = array(
				'nombre'          => $item['player']->name,
				'rival'           => $item['rival'],
				'categoria'       => $partido->categoria,
				'ronda'           => $partido->ronda,
				'fecha_formateada'=> $fecha_formateada,
				'hora'            => $hora_fmt,
			);
			$body = $this->load->view('email/programacion_confirm.php', $data, true);
			$this->email
				->from('secretaria@baltc.net', 'Secretaría BALTC')
				->to($item['player']->email)
				->subject('Tu partido fue programado - Torneo BALTC')
				->message($body)
				->send(); // EMAILS DESHABILITADOS
		}
	}

	private function _sendResultadoEmail($partido_id, $ganador_id, $score) {
		$partido = $this->Partido_model->getById($partido_id);
		if(!$partido) return;

		$ganador = $this->User->getById($ganador_id);
		if(!$ganador || !filter_var($ganador->email, FILTER_VALIDATE_EMAIL)) return;

		$perdedor_id = $partido->jugador1_id == $ganador_id ? $partido->jugador2_id : $partido->jugador1_id;
		$perdedor = $this->User->getById($perdedor_id);

		$data = array(
			'nombre'   => $ganador->name,
			'ganador'  => $ganador->name,
			'perdedor' => $perdedor ? $perdedor->name : '',
			'categoria'=> $partido->categoria,
			'ronda'    => $partido->ronda,
			'score'    => $score
		);

		$this->load->library('email');
		$body = $this->load->view('email/resultado_confirm.php', $data, true);
		$this->email->initialize(array());
		$this->email
			->from('secretaria@baltc.net', 'Secretaría BALTC')
			->to($ganador->email)
			->subject('Resultado de tu partido - Torneo BALTC')
			->message($body)
			->send();
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
			    ->send(); // EMAILS DESHABILITADOS
		}

		return $datos;
	}

	public function partidos() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']    = 'Partidos';
		$d['token']     = $this->protect->eToken();
		$d['section']   = 'admin-partidos';
		$d['matches']   = $this->Partido_model->getAll();
		$d['categories'] = $this->Reservation->getCategories();
		$d['inscriptos'] = $this->Administrator->getInscriptos();
		$this->load->view('admin/header', $d);
		$this->load->view('admin/partidos');
		$this->load->view('admin/footer');
	}

	public function addPartido() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$post = $this->input->post();
		if(empty($post['category']) || empty($post['jugador1_id']) || empty($post['jugador2_id'])) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Faltan datos.'));
		}
		$add = $this->Partido_model->add($post);
		$this->protect->ajaxDie(array('action' => $add !== false));
	}

	public function editPartido() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$post = $this->input->post();
		$id = intval($post['id']);
		$updated = $this->Partido_model->edit($id, $post);

		// Si se programó fecha/hora, enviar email a ambos jugadores
		if($updated !== false && !empty($post['fecha'])) {
// EMAILS DESHABILITADOS // 			$this->_sendProgramacionEmail($id, $post['fecha'], $post['hora']);
		}

		// Si se cargó un ganador, intentar avanzar el bracket y enviar email
		if($updated !== false && !empty($post['ganador_id'])) {
			$this->_avanzarBracket($id, intval($post['ganador_id']), $post);
			$this->_sendResultadoEmail($id, intval($post['ganador_id']), $post['score']);
		}

		$this->protect->ajaxDie(array('action' => $updated !== false));
	}

	private function _avanzarBracket($partido_id, $ganador_id) {
		// Obtener el partido actual
		$partido = $this->Partido_model->getById($partido_id);
		if(!$partido) return;

		$rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');
		$ronda_idx = array_search($partido->ronda, $rondas);
		if($ronda_idx === false || $ronda_idx >= count($rondas) - 1) return;

		$siguiente_ronda = $rondas[$ronda_idx + 1];

		// Obtener todos los partidos de esta ronda/categoria/gender ordenados por id
		$partidos_ronda = $this->Partido_model->getByRonda($partido->category, $partido->gender, $partido->ronda);
		if(!$partidos_ronda) return;

		// Encontrar posicion del partido actual en la ronda
		$pos = -1;
		foreach($partidos_ronda as $i => $p) {
			if($p->id == $partido_id) { $pos = $i; break; }
		}
		if($pos === -1) return;

		// El "hermano" es el partido adyacente (par/impar)
		$hermano_pos = ($pos % 2 === 0) ? $pos + 1 : $pos - 1;
		if(!isset($partidos_ronda[$hermano_pos])) return;
		$hermano = $partidos_ronda[$hermano_pos];

		// Solo avanzar si el hermano también tiene ganador
		if(empty($hermano->ganador_id)) return;

		// Determinar jugador1 y jugador2 del siguiente partido
		// El de posicion par va como jugador1, el impar como jugador2
		if($pos % 2 === 0) {
			$j1 = $ganador_id;
			$j2 = intval($hermano->ganador_id);
		} else {
			$j1 = intval($hermano->ganador_id);
			$j2 = $ganador_id;
		}

		// Ver si ya existe el partido de siguiente ronda entre estos dos
		$existe = $this->Partido_model->existePartido($partido->category, $partido->gender, $siguiente_ronda, $j1, $j2);
		if($existe) return;

		// Crear el partido de la siguiente ronda
		$gender_val = !empty($partido->gender) ? $partido->gender : $post['gender'];
		// bracket_pos de la siguiente ronda = floor(pos_actual / 2)
		$next_pos = floor(min($pos, $hermano_pos) / 2);
		$this->Partido_model->add(array(
			'category'    => $partido->category,
			'gender'      => $gender_val,
			'ronda'       => $siguiente_ronda,
			'bracket_pos' => $next_pos,
			'jugador1_id' => $j1,
			'jugador2_id' => $j2,
			'score'       => null,
			'ganador_id'  => null
		));
	}

	public function draws() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']    = 'Draw';
		$d['token']     = $this->protect->eToken();
		$d['section']   = 'admin-draws';
		$d['categories'] = $this->Reservation->getCategories();
		$this->load->view('admin/header', $d);
		$this->load->view('admin/draws');
		$this->load->view('admin/footer');
	}

	public function getDrawsDisponibles() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$sql = "SELECT DISTINCT m.category, m.gender, c.name as categoria
				FROM matches m
				JOIN category c ON c.id = m.category
				ORDER BY m.category ASC, m.gender ASC";
		$q = $this->db->query($sql);
		$this->protect->ajaxDie(array('action'=>true, 'draws'=> $q->num_rows() > 0 ? $q->result() : array()));
	}

	public function getDrawData() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$category = intval($this->input->post('category'));
		$gender   = $this->input->post('gender', true);
		$partidos = $this->Partido_model->getByCategoryAndGender($category, $gender);
		$q = $this->db->where('category', $category)->where('gender', $gender)->order_by('numero ASC')->get('sembrados');
		$sembrados = array();
		if($q->num_rows() > 0) {
			foreach($q->result() as $s) {
				$sembrados[$s->partner_id] = $s->numero;
			}
		}
		$this->protect->ajaxDie(array('action' => !empty($partidos), 'partidos' => $partidos ?: array(), 'sembrados' => $sembrados));
	}

	public function toggleInscripciones() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$value = $this->input->post('value') ? '1' : '0';
		$exists = $this->db->where('key', 'inscripciones_abiertas')->get('settings');
		if($exists->num_rows() > 0) {
			$this->db->where('key', 'inscripciones_abiertas')->update('settings', array('value' => $value));
		} else {
			$this->db->insert('settings', array('key' => 'inscripciones_abiertas', 'value' => $value));
		}
		$this->protect->ajaxDie(array('action' => true));
	}

	public function jugadores() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']     = 'Jugadores';
		$d['token']      = $this->protect->eToken();
		$d['section']    = 'admin-jugadores';
		$d['jugadores']  = $this->Administrator->getJugadores();
		$d['categories'] = $this->Reservation->getCategories();
		$this->load->view('admin/header', $d);
		$this->load->view('admin/jugadores');
		$this->load->view('admin/footer');
	}

	public function buscarPartner() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$q = trim($this->input->post('q', true));
		if(empty($q)) $this->protect->ajaxDie(array('action'=>false, 'partners'=>array()));
		$this->db->select('id, name, dni, email, gender')
			->from('partners')
			->group_start()
				->like('name', $q)
				->or_like('dni', $q)
			->group_end()
			->order_by('name ASC')
			->limit(20);
		$res = $this->db->get();
		$this->protect->ajaxDie(array('action'=>true, 'partners'=> $res->num_rows() > 0 ? $res->result() : array()));
	}

	public function inscribirPartner() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$partner_id = intval($this->input->post('partner_id'));
		$category   = intval($this->input->post('category'));
		if(!$partner_id || !$category) $this->protect->ajaxDie(array('action'=>false, 'msg'=>'Datos incompletos.'));
		// Verificar que no esté ya inscripto en esta categoría
		$ya = $this->db
			->select('r.id')
			->from('reservations r')
			->join('reservations_partners rp', 'rp.reservation_id = r.id')
			->where('rp.partner_id', $partner_id)
			->where('r.category', $category)
			->get();
		if($ya->num_rows() > 0) {
			$this->protect->ajaxDie(array('action'=>false, 'msg'=>'Este jugador ya está inscripto en esa categoría.'));
		}
		// Crear reserva y vincular
		$this->db->insert('reservations', array('category' => $category));
		$reserva_id = $this->db->insert_id();
		$this->db->insert('reservations_partners', array('partner_id' => $partner_id, 'reservation_id' => $reserva_id));
		// Enviar email de confirmación
		$partner = $this->User->getById($partner_id);
		$cat = $this->db->where('id', $category)->get('category')->row();
		if($partner && filter_var($partner->email, FILTER_VALIDATE_EMAIL)) {
			$this->load->library('email');
			$data = array(
				'nombre'   => ucwords(strtolower(str_replace(',', '', $partner->name))),
				'partner'  => '',
				'category' => $cat ? $cat->name : '',
				'p'        => array(),
				'user'     => $partner,
			);
			$body = $this->load->view('email/reservation_confirm.php', $data, true);
			$this->email->initialize(array());
			$this->email
				->from('secretaria@baltc.net', 'Secretaría BALTC')
				->to($partner->email)
				->subject('Ya tenés tu inscripción confirmada')
				->message($body)
				->send();
		}
		$this->protect->ajaxDie(array('action'=>true));
	}

	public function addJugador() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$post = $this->input->post();
		if(empty($post['name']) || empty($post['dni']) || empty($post['category'])) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Faltan datos obligatorios.'));
		}
		$id = $this->Administrator->addJugador($post);
		$this->protect->ajaxDie(array('action' => $id > 0));
	}

	public function editJugador() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$post = $this->input->post();
		$ok = $this->Administrator->editJugador(intval($post['id']), intval($post['reserva_id']), $post);
		$this->protect->ajaxDie(array('action' => $ok));
	}

	public function deleteJugador() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$id = intval($this->input->post('id'));
		$reserva_id = intval($this->input->post('reserva_id'));
		$ok = $this->Administrator->deleteJugador($id, $reserva_id);
		$this->protect->ajaxDie(array('action' => $ok));
	}

	public function sorteo() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']    = 'Sorteo';
		$d['token']     = $this->protect->eToken();
		$d['section']   = 'admin-sorteo';
		$d['categories'] = $this->Reservation->getCategories();
		$d['default_cat'] = $this->input->get('cat', true);
		$d['default_gen'] = $this->input->get('gen', true);
		$this->load->view('admin/header', $d);
		$this->load->view('admin/sorteo');
		$this->load->view('admin/footer');
	}

	public function getInscriptosByCategory() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$category = intval($this->input->post('category'));
		$gender = $this->input->post('gender', true);
		$jugadores = $this->Administrator->getInscriptosByCategory($category, $gender);
		$this->protect->ajaxDie(array('action' => !empty($jugadores), 'jugadores' => $jugadores ?: array()));
	}

	public function confirmarSorteo() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$category = intval($this->input->post('category'));
		$gender   = $this->input->post('gender', true);
		$jugadores = json_decode($this->input->post('jugadores'), true);

		if(empty($jugadores)) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Sin jugadores.'));
		}

		// Borrar partidos existentes de esta categoria + genero
		$this->Partido_model->deleteByCategoryAndGender($category, $gender);
		// Guardar sembrados
		$this->db->delete('sembrados', array('category' => $category, 'gender' => $gender));
		$sembrados_post = json_decode($this->input->post('sembrados'), true);
		if(!empty($sembrados_post)) {
			foreach($sembrados_post as $numero => $partner_id) {
				if($partner_id) {
					$this->db->insert('sembrados', array(
						'partner_id' => intval($partner_id),
						'category'   => $category,
						'gender'     => $gender,
						'numero'     => intval($numero) + 1
					));
				}
			}
		}

		// El frontend manda bracketFinal con posiciones exactas
		$n = 0;
		foreach($jugadores as $j) { if($j !== null) $n++; }
		$size = 1;
		while($size < $n) $size *= 2;

		// Asegurar tamaño correcto
		while(count($jugadores) < $size) $jugadores[] = null;
		$jugadores = array_slice($jugadores, 0, $size);

		// Eliminar null+null: si un par tiene ambos nulls, tomar un jugador
		// de otro par que tiene jugador solo (jugador+null) y moverlo aquí
		// Esto garantiza que cada par tenga al menos 1 jugador
		for($i = 0; $i < $size; $i += 2) {
			if($jugadores[$i] === null && $jugadores[$i+1] === null) {
				// Buscar un par que tenga jugador solo para "robarle" uno
				for($k = 0; $k < $size; $k += 2) {
					$k1 = $jugadores[$k];
					$k2 = $jugadores[$k+1];
					if($k !== $i && $k1 !== null && $k2 !== null) {
						// Par con dos jugadores: mover k2 al par vacío
						$jugadores[$i] = $k2;
						$jugadores[$k+1] = null;
						break;
					}
				}
			}
		}

				// Ronda de arranque
		$rondasNombres = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');
		$mapa = array(32=>0, 16=>1, 8=>2, 4=>3, 2=>4);
		$rondaInicio = isset($mapa[$size]) ? $mapa[$size] : 0;

		// Generar partidos respetando el array del frontend
		$partidos = array();
		for($i = 0; $i < $size; $i += 2) {
			$j1 = isset($jugadores[$i])   ? $jugadores[$i]   : null;
			$j2 = isset($jugadores[$i+1]) ? $jugadores[$i+1] : null;
			$bp = $i / 2;
			if($j1 && $j2) {
				$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>intval($j1),'jugador2_id'=>intval($j2),'score'=>null,'ganador_id'=>null);
			} elseif($j1) {
				$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>intval($j1),'jugador2_id'=>null,'score'=>'BYE','ganador_id'=>intval($j1));
			} elseif($j2) {
				$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>null,'jugador2_id'=>intval($j2),'score'=>'BYE','ganador_id'=>intval($j2));
			}
			// null+null → slot vacío, no se guarda
		}

		$ok = $this->Partido_model->addBatch($partidos);

		// Avanzar BYEs automáticamente: si dos partidos adyacentes ambos tienen ganador, crear siguiente ronda
		if($ok) {
			$this->_avanzarByesSorteo($partidos, $category, $gender, $rondasNombres, $rondaInicio, $size);
		}

		$this->protect->ajaxDie(array('action' => $ok));
	}

	private function _avanzarByesSorteo($partidos, $category, $gender, $rondasNombres, $rondaInicio, $bracketSize = null) {
		$siguiente_ronda = isset($rondasNombres[$rondaInicio + 1]) ? $rondasNombres[$rondaInicio + 1] : null;
		if(!$siguiente_ronda) return;

		// Indexar por bracket_pos
		$porPos = array();
		foreach($partidos as $p) {
			$porPos[intval($p['bracket_pos'])] = $p;
		}

		// totalSlots = cantidad de partidos en esta ronda = bracketSize / 2
		// En cada ronda siguiente se divide a la mitad
		if($bracketSize) {
			$totalSlots = (int)($bracketSize / 2);
		} else {
			$maxPos = empty($porPos) ? 0 : max(array_keys($porPos));
			$totalSlots = $maxPos + 1;
			if($totalSlots % 2 !== 0) $totalSlots++;
		}

		$nuevos = array();
		for($i = 0; $i < $totalSlots; $i += 2) {
			$p1 = isset($porPos[$i])   ? $porPos[$i]   : null;
			$p2 = isset($porPos[$i+1]) ? $porPos[$i+1] : null;
			// Solo crear si AMBOS tienen ganador_id
			if($p1 && $p2 && !empty($p1['ganador_id']) && !empty($p2['ganador_id'])) {
				$nuevos[] = array(
					'category'    => $category,
					'gender'      => $gender,
					'ronda'       => $siguiente_ronda,
					'bracket_pos' => (int)($i / 2),
					'jugador1_id' => intval($p1['ganador_id']),
					'jugador2_id' => intval($p2['ganador_id']),
					'score'       => null,
					'ganador_id'  => null
				);
			}
		}
		if(!empty($nuevos)) {
			$this->Partido_model->addBatch($nuevos);
			// Pasar bracketSize / 2 para la siguiente ronda
			$this->_avanzarByesSorteo($nuevos, $category, $gender, $rondasNombres, $rondaInicio + 1, $bracketSize ? (int)($bracketSize / 2) : null);
		}
	}

	public function deletePartido() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$id = intval($this->input->post('id'));
		$this->protect->ajaxDie(array('action' => $this->Partido_model->delete($id) > 0));
	}

	// ── RECORDATORIO DEADLINE ──────────────────────────────

	public function previewRecordatorio() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$category = $this->input->post('category', true);
		$gender   = $this->input->post('gender', true);
		$ronda    = $this->input->post('ronda', true);
		$partidos = $this->_getPartidosPendientes($category, $gender, $ronda);
		$jugadores_ids = $this->_getJugadoresDePartidos($partidos);
		// Rondas con conteo
		$rondas = array();
		foreach($partidos as $p) {
			if(!isset($rondas[$p->ronda])) $rondas[$p->ronda] = 0;
			$rondas[$p->ronda]++;
		}
		$rondas_arr = array();
		foreach($rondas as $r => $count) {
			$rondas_arr[] = array('ronda' => $r, 'pendientes' => $count);
		}
		// Lista de enfrentamientos j1 vs j2
		$partidos_lista = array();
		foreach($partidos as $p) {
			$j1 = !empty($p->jugador1_id) ? $this->User->getById($p->jugador1_id) : null;
			$j2 = !empty($p->jugador2_id) ? $this->User->getById($p->jugador2_id) : null;
			$partidos_lista[] = array(
				'j1' => $j1 ? $j1->name : '?',
				'j2' => $j2 ? $j2->name : '?'
			);
		}
		$this->protect->ajaxDie(array(
			'action'         => true,
			'partidos'       => count($partidos),
			'total'          => count($jugadores_ids),
			'rondas'         => $rondas_arr,
			'partidos_lista' => $partidos_lista
		));
	}

	public function enviarRecordatorio() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$deadline = $this->input->post('deadline', true);
		$category = $this->input->post('category', true);
		$gender   = $this->input->post('gender', true);
		$ronda    = $this->input->post('ronda', true);
		if(!$deadline) $this->protect->ajaxDie(array('action'=>false, 'msg'=>'Fecha requerida.'));
		$meses = array('01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre');
		$parts = explode('-', $deadline);
		$fecha_str = intval($parts[2]) . ' de ' . $meses[$parts[1]] . ' de ' . $parts[0];
		$partidos = $this->_getPartidosPendientes($category, $gender, $ronda);
		// Guardar deadline en cada partido afectado
		foreach($partidos as $p) {
			$this->db->where('id', $p->id)->update('matches', array('deadline' => $deadline));
		}
		$this->load->library('email');
		$enviados = 0;
		foreach($partidos as $p) {
			$jugadores_partido = array();
			if(!empty($p->jugador1_id)) $jugadores_partido[] = array('jug' => $this->User->getById($p->jugador1_id), 'rival_id' => $p->jugador2_id);
			if(!empty($p->jugador2_id)) $jugadores_partido[] = array('jug' => $this->User->getById($p->jugador2_id), 'rival_id' => $p->jugador1_id);
			foreach($jugadores_partido as $item) {
				$jug = $item['jug'];
				if(!$jug || !filter_var($jug->email, FILTER_VALIDATE_EMAIL)) continue;
				$rival = !empty($item['rival_id']) ? $this->User->getById($item['rival_id']) : null;
				$data = array(
					'nombre'    => $jug->name,
					'rival'     => $rival ? $rival->name : 'Tu rival',
					'categoria' => $p->categoria,
					'ronda'     => $p->ronda,
					'deadline'  => $fecha_str,
				);
				$body = $this->load->view('email/recordatorio_deadline.php', $data, true);
				$this->email->initialize(array());
				$this->email
					->from('secretaria@baltc.net', 'Secretaría BALTC')
					->to($jug->email)
					->subject('Recordatorio: fecha límite para tu partido - Torneo BALTC')
					->message($body)
					->send();
				$enviados++;
			}
		}
		$this->protect->ajaxDie(array('action'=>true, 'enviados'=>$enviados));
	}

	private function _getPartidosPendientes($category = '', $gender = '', $ronda = '') {
		$this->db->select('m.*, c.name as categoria')
			->from('matches m')
			->join('category c', 'c.id = m.category')
			->where('m.ganador_id IS NULL')
			->where('m.score IS NULL');
		if($category) $this->db->where('m.category', $category);
		if($gender)   $this->db->where('m.gender', $gender);
		if($ronda)    $this->db->where('m.ronda', $ronda);
		$q = $this->db->get();
		return $q->num_rows() > 0 ? $q->result() : array();
	}

	private function _getJugadoresDePartidos($partidos) {
		$ids = array();
		foreach($partidos as $p) {
			if(!empty($p->jugador1_id)) $ids[] = $p->jugador1_id;
			if(!empty($p->jugador2_id)) $ids[] = $p->jugador2_id;
		}
		return array_unique($ids);
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
		    ->send(); // EMAILS DESHABILITADOS
	}
}
