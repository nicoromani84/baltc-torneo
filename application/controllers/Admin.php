<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Reservation');
		$this->load->model('Administrator');
		$this->load->model('Partido_model');
		$this->load->model('LoginLog');
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
		$d['readonly']   = $this->Administrator->isReadOnly();
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

	public function singles() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']     = 'Singles - Admin';
		$d['token']      = $this->protect->eToken();
		$d['section']    = 'admin-singles';
		$d['readonly']   = $this->Administrator->isReadOnly();
		$d['jugadores']  = $this->Administrator->getJugadoresByTournament(false, false, 'singles');
		$d['categories'] = $this->Administrator->getAllCategories();
		$q_s = $this->db->where('key', 'inscripciones_abiertas')->get('settings');
		$d['inscripciones_abiertas'] = ($q_s->num_rows() > 0 && $q_s->row()->value == '1');
		$this->load->view('admin/header',$d);
		$this->load->view('admin/singles');
		$this->load->view('admin/footer');
	}

	public function loginLogs() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']     = 'Login Logs - Admin';
		$d['token']      = $this->protect->eToken();
		$d['section']    = 'admin-loginlogs';
		$d['logs']       = $this->LoginLog->getLogs(200);
		$this->load->view('admin/header',$d);
		$this->load->view('admin/loginlogs');
		$this->load->view('admin/footer');
	}

	public function partners() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']     = 'Partners - Admin';
		$d['token']      = $this->protect->eToken();
		$d['section']    = 'admin-partners';
		$d['partners']   = $this->db->order_by('name', 'ASC')->get('partners')->result();
		$this->load->view('admin/header',$d);
		$this->load->view('admin/partners');
		$this->load->view('admin/footer');
	}

	public function editPartner() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if (!$this->Administrator->isLogged()) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'No autorizado'));
		}

		$id = $this->input->post('id', true);
		$name = $this->input->post('name', true);
		$dni = $this->input->post('dni', true);
		$gender = $this->input->post('gender', true);
		$email = $this->input->post('email', true);

		if (!$id || !$name || !$dni || !$gender) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Campos requeridos'));
		}

		$result = $this->Administrator->editPartner($id, $name, $dni, $gender, $email);
		$this->protect->ajaxDie(array('action' => $result, 'msg' => $result ? 'Partner actualizado' : 'Error'));
	}

	public function editParejaCategory() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if (!$this->Administrator->isLogged()) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'No autorizado'));
		}

		$reservation_id = $this->input->post('reservation_id', true);
		$category_id = $this->input->post('category_id', true);

		if (!$reservation_id || !$category_id) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Parámetros requeridos'));
		}

		$result = $this->Administrator->editParejaCategory($reservation_id, $category_id);
		$this->protect->ajaxDie(array('action' => $result, 'msg' => $result ? 'Categoría actualizada' : 'Error'));
	}

	public function deletePartner() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if (!$this->Administrator->isLogged()) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'No autorizado'));
		}

		$id = $this->input->post('id', true);
		$result = $this->db->where('id', $id)->delete('partners');
		$this->protect->ajaxDie(array('action' => $result, 'msg' => $result ? 'Partner eliminado' : 'Error'));
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
						'id'			=> $u->id,
						'name'			=> $u->username,
						'role'			=> isset($u->role) ? $u->role : 'admin'
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
		$tournament_type = $this->input->post('tournament_type', true);
		if(!$tournament_type) $tournament_type = 'singles';

		if($tournament_type === 'doubles') {
			$parejas = $this->Administrator->getPairesByTournament($gender, $category, $tournament_type);
			$res = array('parejas' => $parejas);
		} else {
			$jugadores = $this->Administrator->getJugadoresByTournament($gender, $category, $tournament_type);
			$res = array('jugadores' => $jugadores);
		}

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
		$d['readonly']  = $this->Administrator->isReadOnly();

		$d['matches'] = $this->Partido_model->getAll();

		$allCats = $this->Reservation->getCategories();
		$this->db->where('tournament_type', 'doubles');
		$doublesQ = $this->db->get('reservations');
		$doblesCats = array();
		foreach($doublesQ->result() as $r) {
			$doblesCats[$r->category] = true;
		}
		$filteredCats = array();
		foreach($allCats as $c) {
			if(isset($doblesCats[$c->id])) {
				$filteredCats[] = $c;
			}
		}
		$d['categories'] = $filteredCats;

		$d['inscriptos'] = $this->Administrator->getInscriptos('doubles');
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
		$d['readonly']  = $this->Administrator->isReadOnly();
		$d['categories'] = $this->Reservation->getCategories();

		$this->load->view('admin/header', $d);
		$this->load->view('admin/draws');
		$this->load->view('admin/footer');
	}

	public function cleanupMatches() {
		$key = $this->input->get('key');
		if($key !== 'cleanup2024') die('Unauthorized');

		$sql = "DELETE FROM matches
				WHERE reservation_id NOT IN (
					SELECT id FROM reservations WHERE tournament_type = 'doubles'
				) AND reservation_id IS NOT NULL";
		$this->db->query($sql);
		echo "Draws de singles eliminados. La BD ahora solo tiene dobles.";
		die;
	}

	public function cleanupAllMatches() {
		$key = $this->input->get('key');
		if($key !== 'cleanup2024') die('Unauthorized');

		$this->db->query("DELETE FROM matches");
		echo "Todos los matches eliminados.";
		die;
	}

	public function dbStructure() {
		$key = $this->input->get('key');
		if($key !== 'cleanup2024') die('Unauthorized');

		header('Content-Type: application/json');

		// Obtener todas las tablas
		$tables_q = $this->db->query("SHOW TABLES")->result();
		$tables = array();
		foreach($tables_q as $t) {
			foreach($t as $table) {
				$tables[] = $table;
			}
		}

		$result = array('all_tables' => $tables);

		// Describir cada tabla
		foreach($tables as $table) {
			$columns = $this->db->query("DESCRIBE $table")->result();
			$result[$table] = $columns;
		}

		echo json_encode($result, JSON_PRETTY_PRINT);
		die;
	}

	public function debugMatches() {
		$key = $this->input->get('key');
		if($key !== 'cleanup2024') die('Unauthorized');

		header('Content-Type: application/json');

		$result = array();

		// Ver qué hay en matches
		$result['matches_count'] = $this->db->count_all('matches');
		$result['matches'] = $this->db->limit(5)->get('matches')->result();

		// Ver reservations_partners
		$result['reservations_partners_count'] = $this->db->count_all('reservations_partners');
		$result['reservations_partners'] = $this->db->limit(10)->get('reservations_partners')->result();

		// Ver un partido específico con su subquery
		if($result['matches_count'] > 0) {
			$first_match = $result['matches'][0];
			$sql = "SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = " . intval($first_match->jugador1_id);
			$q = $this->db->query($sql);
			$result['test_subquery_j1'] = $q->row_array();
		}

		echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		die;
	}

	public function getDrawsDisponibles() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		// Debug: check raw matches
		$allQ = $this->db->get('matches');
		$all = $allQ->result();

		$sql = "SELECT DISTINCT m.category, m.gender, c.name as categoria
				FROM matches m
				JOIN category c ON c.id = m.category
				ORDER BY m.category ASC, m.gender ASC";
		$q = $this->db->query($sql);

		$this->protect->ajaxDie(array(
			'action'=>true,
			'draws'=> $q->num_rows() > 0 ? $q->result() : array(),
			'debug' => array(
				'total_matches' => count($all),
				'query_matches' => $q->num_rows(),
				'sample' => count($all) > 0 ? $all[0] : null
			)
		));
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
				->send(); // EMAILS DESHABILITADOS
		}
		$this->protect->ajaxDie(array('action'=>true));
	}

	public function addJugador() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if($this->Administrator->isReadOnly()) $this->protect->ajaxDie(array('action'=>false,'msg'=>'Sin permisos.'));
		$post = $this->input->post();
		if(empty($post['name']) || empty($post['dni']) || empty($post['category'])) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Faltan datos obligatorios.'));
		}
		// Si el DNI ya existe, inscribir al jugador existente en lugar de crear duplicado
		$existente = $this->User->check($post['dni']);
		if($existente) {
			// Verificar que no esté ya en esta categoría específica
			$ya = $this->db->select('r.id')->from('reservations r')
				->join('reservations_partners rp','rp.reservation_id = r.id')
				->where('rp.partner_id', $existente->id)
				->where('r.category', intval($post['category']))->get();
			if($ya->num_rows() > 0) {
				$this->protect->ajaxDie(array('action'=>false,'msg'=>strtolower($existente->name).' ya está en esta categoría.'));
			}
			$this->db->insert('reservations', array('category' => intval($post['category'])));
			$reserva_id = $this->db->insert_id();
			$this->db->insert('reservations_partners', array('partner_id'=>$existente->id,'reservation_id'=>$reserva_id));
			$this->protect->ajaxDie(array('action'=>true,'msg'=>'Jugador existente inscripto en la nueva categoría.'));
		}
		$id = $this->Administrator->addJugador($post);
		$this->protect->ajaxDie(array('action' => $id > 0));
	}

	public function editJugador() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if($this->Administrator->isReadOnly()) $this->protect->ajaxDie(array('action'=>false,'msg'=>'Sin permisos.'));
		$post = $this->input->post();
		$ok = $this->Administrator->editJugador(intval($post['id']), intval($post['reserva_id']), $post);
		$this->protect->ajaxDie(array('action' => $ok));
	}

	public function deleteJugador() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if($this->Administrator->isReadOnly()) $this->protect->ajaxDie(array('action'=>false,'msg'=>'Sin permisos.'));
		$id = intval($this->input->post('id'));
		$reserva_id = intval($this->input->post('reserva_id'));
		$ok = $this->Administrator->deleteJugador($id, $reserva_id);
		$this->protect->ajaxDie(array('action' => $ok));
	}

	public function sorteo() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		if ($this->Administrator->isReadOnly()) redirect(base_url('/admin'));
		$d['titulo']    = 'Sorteo';
		$d['token']     = $this->protect->eToken();
		$d['section']   = 'admin-sorteo';

		$allCats = $this->Reservation->getCategories();
		$this->db->where('tournament_type', 'doubles');
		$doublesQ = $this->db->get('reservations');
		$doblesCats = array();
		foreach($doublesQ->result() as $r) {
			$doblesCats[$r->category] = true;
		}

		$filteredCats = array();
		foreach($allCats as $c) {
			if(isset($doblesCats[$c->id])) {
				$filteredCats[] = $c;
			}
		}

		$d['categories'] = $filteredCats;
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
		$tournament_type = $this->input->post('tournament_type', true);
		if(!$tournament_type) $tournament_type = 'singles';
		$jugadores = $this->Administrator->getInscriptosByCategory($category, $gender, $tournament_type);
		$this->protect->ajaxDie(array('action' => !empty($jugadores), 'jugadores' => $jugadores ?: array()));
	}

	public function confirmarSorteo() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if($this->Administrator->isReadOnly()) $this->protect->ajaxDie(array('action'=>false,'msg'=>'Sin permisos.'));
		$category = intval($this->input->post('category'));
		$gender   = $this->input->post('gender', true);
		$tournament_type = $this->input->post('tournament_type', true);
		if(!$tournament_type) $tournament_type = 'singles';
		$jugadores = json_decode($this->input->post('jugadores'), true);

		if(empty($jugadores)) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Sin jugadores.'));
		}

		// Para dobles, los reservation_ids ya son los IDs que necesito
		// Los guardo tal cual (son los IDs de las reservations que contienen la pareja)
		if($tournament_type !== 'doubles') {
			// Para singles, convertir a partner_ids
			// (El resto del código sigue igual pero para singles)
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

			// Para dobles, j1 y j2 son reservation_ids (contienen ambos partners)
			// Para singles, j1 y j2 son partner_ids
			if($tournament_type === 'doubles') {
				if($j1 && $j2) {
					$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>intval($j1),'jugador2_id'=>intval($j2),'score'=>null,'ganador_id'=>null);
				} elseif($j1) {
					$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>intval($j1),'jugador2_id'=>null,'score'=>'BYE','ganador_id'=>null);
				} elseif($j2) {
					$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>intval($j2),'jugador2_id'=>null,'score'=>'BYE','ganador_id'=>null);
				}
			} else {
				if($j1 && $j2) {
					$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>intval($j1),'jugador2_id'=>intval($j2),'score'=>null,'ganador_id'=>null);
				} elseif($j1) {
					$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>intval($j1),'jugador2_id'=>null,'score'=>'BYE','ganador_id'=>null);
				} elseif($j2) {
					$partidos[] = array('category'=>$category,'gender'=>$gender,'ronda'=>$rondasNombres[$rondaInicio],'bracket_pos'=>$bp,'jugador1_id'=>intval($j2),'jugador2_id'=>null,'score'=>'BYE','ganador_id'=>null);
				}
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
			if(!$p1 || !$p2) continue;

			// Para BYEs (score='BYE'), el "ganador" es jugador1_id. Para partidos normales es ganador_id.
			$g1 = ($p1['score'] === 'BYE') ? $p1['jugador1_id'] : $p1['ganador_id'];
			$g2 = ($p2['score'] === 'BYE') ? $p2['jugador1_id'] : $p2['ganador_id'];

			// Solo crear si AMBOS tienen ganador definido
			if(!empty($g1) && !empty($g2)) {
				$nuevos[] = array(
					'category'    => $category,
					'gender'      => $gender,
					'ronda'       => $siguiente_ronda,
					'bracket_pos' => (int)($i / 2),
					'jugador1_id' => intval($g1),
					'jugador2_id' => intval($g2),
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
			// Detectar si es dobles intentando obtener partners
			$j1_partners = $this->db->query(
				"SELECT p.name FROM reservations_partners rp
				 JOIN partners p ON p.id = rp.partner_id
				 WHERE rp.reservation_id = ?
				 ORDER BY p.name ASC",
				array($p->jugador1_id)
			)->result();

			$j2_partners = $this->db->query(
				"SELECT p.name FROM reservations_partners rp
				 JOIN partners p ON p.id = rp.partner_id
				 WHERE rp.reservation_id = ?
				 ORDER BY p.name ASC",
				array($p->jugador2_id)
			)->result();

			// Si hay partners en cualquiera, es dobles
			if(!empty($j1_partners) || !empty($j2_partners)) {
				$j1_name = !empty($j1_partners) ? implode(' / ', array_map(function($x) { return $x->name; }, $j1_partners)) : '?';
				$j2_name = !empty($j2_partners) ? implode(' / ', array_map(function($x) { return $x->name; }, $j2_partners)) : '?';
			} else {
				// Singles: obtener jugadores individuales
				$j1 = $this->User->getById($p->jugador1_id);
				$j2 = $this->User->getById($p->jugador2_id);
				$j1_name = $j1 ? $j1->name : '?';
				$j2_name = $j2 ? $j2->name : '?';
			}

			$partidos_lista[] = array(
				'j1' => $j1_name,
				'j2' => $j2_name
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
		$test_email = $this->input->post('test_email', true);
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
			// Detectar si es dobles intentando obtener partners
			$j1_partners = $this->db->query(
				"SELECT p.name, p.email FROM reservations_partners rp
				 JOIN partners p ON p.id = rp.partner_id
				 WHERE rp.reservation_id = ?
				 ORDER BY p.name ASC",
				array($p->jugador1_id)
			)->result();

			$j2_partners = $this->db->query(
				"SELECT p.name, p.email FROM reservations_partners rp
				 JOIN partners p ON p.id = rp.partner_id
				 WHERE rp.reservation_id = ?
				 ORDER BY p.name ASC",
				array($p->jugador2_id)
			)->result();

			// Si hay partners, es dobles. Si no, es singles.
			if(!empty($j1_partners) && !empty($j2_partners)) {
				$j1_names = implode(' / ', array_map(function($x) { return $x->name; }, $j1_partners));
				$j2_names = implode(' / ', array_map(function($x) { return $x->name; }, $j2_partners));

				// Enviar a J1
				$emails_enviados = array();
				foreach($j1_partners as $partner) {
					if(!$partner || !filter_var($partner->email, FILTER_VALIDATE_EMAIL)) continue;
					if(in_array($partner->email, $emails_enviados)) continue;
					$emails_enviados[] = $partner->email;

					$data = array(
						'nombre'    => $partner->name,
						'rival'     => $j2_names,
						'categoria' => $p->categoria,
						'ronda'     => $p->ronda,
						'deadline'  => $fecha_str,
					);
					$body = $this->load->view('email/recordatorio_deadline.php', $data, true);
					$this->email->initialize(array());
					$this->email
						->from('secretaria@baltc.net', 'Secretaría BALTC')
						->to($test_email ?: $partner->email)
						->subject('Recordatorio: fecha límite para tu partido - Torneo BALTC')
						->message($body)
						->send();
					$enviados++;
				}

				// Enviar a J2
				foreach($j2_partners as $partner) {
					if(!$partner || !filter_var($partner->email, FILTER_VALIDATE_EMAIL)) continue;
					if(in_array($partner->email, $emails_enviados)) continue;
					$emails_enviados[] = $partner->email;

					$data = array(
						'nombre'    => $partner->name,
						'rival'     => $j1_names,
						'categoria' => $p->categoria,
						'ronda'     => $p->ronda,
						'deadline'  => $fecha_str,
					);
					$body = $this->load->view('email/recordatorio_deadline.php', $data, true);
					$this->email->initialize(array());
					$this->email
						->from('secretaria@baltc.net', 'Secretaría BALTC')
						->to($test_email ?: $partner->email)
						->subject('Recordatorio: fecha límite para tu partido - Torneo BALTC')
						->message($body)
						->send();
					$enviados++;
				}
			} else {
				// Singles: obtener jugadores individuales
				$j1_user = $this->User->getById($p->jugador1_id);
				$j2_user = $this->User->getById($p->jugador2_id);
				$all_partners = array_filter(array($j1_user, $j2_user));
				$rival_names = $j2_user ? $j2_user->name : 'Tu rival';

				// Enviar a todos (sin duplicados por email)
				$emails_enviados = array();
				foreach($all_partners as $partner) {
					if(!$partner || !filter_var($partner->email, FILTER_VALIDATE_EMAIL)) continue;
					// Evitar enviar duplicados al mismo email
					if(in_array($partner->email, $emails_enviados)) continue;
					$emails_enviados[] = $partner->email;

					$data = array(
						'nombre'    => $partner->name,
						'rival'     => $rival_names ?: 'Tu rival',
						'categoria' => $p->categoria,
						'ronda'     => $p->ronda,
						'deadline'  => $fecha_str,
					);
					$body = $this->load->view('email/recordatorio_deadline.php', $data, true);
					$this->email->initialize(array());
					$this->email
						->from('secretaria@baltc.net', 'Secretaría BALTC')
						->to($test_email ?: $partner->email)
						->subject('Recordatorio: fecha límite para tu partido - Torneo BALTC')
						->message($body)
						->send();
					$enviados++;
				}
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

	private function _getEmailTemplates() {
		return array(
			'bienvenida' => array(
				'nombre' => '🎾 Bienvenida al torneo',
				'asunto' => '¡Bienvenido al Torneo Interno de Dobles - Open BALTC!',
			),
		);
	}

	public function getEmailTemplates() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$list = array();
		foreach($this->_getEmailTemplates() as $id => $t) {
			$list[] = array('id' => $id, 'nombre' => $t['nombre'], 'asunto' => $t['asunto']);
		}
		$this->protect->ajaxDie(array('action'=>true, 'templates'=>$list));
	}

	public function previewEmailTemplate() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$template_id = $this->input->post('template_id', true);
		$templates = $this->_getEmailTemplates();
		if(!isset($templates[$template_id])) $this->protect->ajaxDie(array('action'=>false, 'msg'=>'Template no encontrado.'));
		$data = array('nombre' => 'Jugador de Prueba');
		$html = $this->load->view('email/' . $template_id . '.php', $data, true);
		$this->protect->ajaxDie(array('action'=>true, 'html'=>$html, 'asunto'=>$templates[$template_id]['asunto']));
	}

	public function enviarInvitacion2ndChance() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$secret     = 'baltc_2ndchance_2026';
		$orden_rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');

		// Obtener combos category+gender con partidos jugados
		$combos = $this->db->query("
			SELECT DISTINCT m.category, c.name AS cat_name, m.gender
			FROM matches m
			JOIN category c ON c.id = m.category
			WHERE c.name NOT LIKE '%2nd chance%' AND m.ganador_id IS NOT NULL
			ORDER BY m.category ASC, m.gender ASC
		")->result();

		$this->load->library('email');
		$enviados = 0;
		$lista    = array();

		foreach($combos as $combo) {
			// Ronda inicial de esta categoría+género
			$ronda_inicial = null;
			foreach($orden_rondas as $ronda) {
				$r = $this->db->query(
					"SELECT COUNT(*) AS cnt FROM matches WHERE category=? AND gender=? AND ronda=? AND ganador_id IS NOT NULL AND score!='BYE'",
					array($combo->category, $combo->gender, $ronda)
				)->row();
				if($r->cnt > 0) { $ronda_inicial = $ronda; break; }
			}
			if(!$ronda_inicial) continue;

			// Categoría 2nd chance correspondiente
			$cat_2nd = $this->db->where('name', $combo->cat_name . ' 2nd chance')->get('category')->row();
			if(!$cat_2nd) continue;

			// Perdedores de esa ronda inicial
			$losers = $this->db->query("
				SELECT CASE WHEN m.ganador_id=m.jugador1_id THEN m.jugador2_id ELSE m.jugador1_id END AS loser_id
				FROM matches m
				WHERE m.category=? AND m.gender=? AND m.ronda=? AND m.ganador_id IS NOT NULL AND m.score!='BYE'
			", array($combo->category, $combo->gender, $ronda_inicial))->result();

			foreach($losers as $l) {
				$partner = $this->User->getById($l->loser_id);
				if(!$partner || !filter_var($partner->email, FILTER_VALIDATE_EMAIL)) continue;

				$token = md5($partner->id . ':' . $cat_2nd->id . ':' . $secret);
				$link  = base_url('invitacion/aceptar2ndchance') . '?pid=' . $partner->id . '&cid=' . $cat_2nd->id . '&t=' . $token;

				$data = array(
					'nombre'        => $partner->name,
					'categoria'     => $combo->cat_name,
					'ronda_inicial' => $ronda_inicial,
					'link_inscripcion' => $link,
				);
				$body = $this->load->view('email/invitacion_2ndchance.php', $data, true);
				$this->email->initialize(array());
				$this->email
					->from('secretaria@baltc.net', 'Secretaría BALTC')
					->to($partner->email)
					->subject('¿Querés seguir en el torneo? — 2nd Chance BALTC')
					->message($body)
					->send();

				$enviados++;
				$lista[] = array('nombre' => $partner->name, 'email' => $partner->email, 'categoria' => $combo->cat_name . ' 2nd chance');
			}
		}

		$this->protect->ajaxDie(array('action' => true, 'enviados' => $enviados, 'lista' => $lista));
	}

	public function preview2ndChance() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$orden_rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');
		$combos = $this->db->query("
			SELECT DISTINCT m.category, c.name AS cat_name, m.gender
			FROM matches m
			JOIN category c ON c.id = m.category
			WHERE c.name NOT LIKE '%2nd chance%' AND m.ganador_id IS NOT NULL
			ORDER BY m.category ASC, m.gender ASC
		")->result();

		$lista = array();
		foreach($combos as $combo) {
			$ronda_inicial = null;
			foreach($orden_rondas as $ronda) {
				$r = $this->db->query(
					"SELECT COUNT(*) AS cnt FROM matches WHERE category=? AND gender=? AND ronda=? AND ganador_id IS NOT NULL AND score!='BYE'",
					array($combo->category, $combo->gender, $ronda)
				)->row();
				if($r->cnt > 0) { $ronda_inicial = $ronda; break; }
			}
			if(!$ronda_inicial) continue;
			$cat_2nd = $this->db->where('name', $combo->cat_name . ' 2nd chance')->get('category')->row();
			if(!$cat_2nd) continue;

			$losers = $this->db->query("
				SELECT CASE WHEN m.ganador_id=m.jugador1_id THEN m.jugador2_id ELSE m.jugador1_id END AS loser_id
				FROM matches m
				WHERE m.category=? AND m.gender=? AND m.ronda=? AND m.ganador_id IS NOT NULL AND m.score!='BYE'
			", array($combo->category, $combo->gender, $ronda_inicial))->result();

			$gen = $combo->gender === 'M' ? 'Caballeros' : 'Damas';
			foreach($losers as $l) {
				$partner = $this->User->getById($l->loser_id);
				if(!$partner || !filter_var($partner->email, FILTER_VALIDATE_EMAIL)) continue;
				$lista[] = array('nombre' => $partner->name, 'email' => $partner->email, 'categoria' => $combo->cat_name . ' 2nd chance — ' . $gen);
			}
		}
		$this->protect->ajaxDie(array('action'=>true, 'total'=>count($lista), 'lista'=>$lista));
	}

	public function preview2ndChanceEmail() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$data = array(
			'nombre'           => 'ROMANI, NICOLAS',
			'categoria'        => '1ra',
			'ronda_inicial'    => '2da Ronda',
			'link_inscripcion' => base_url('invitacion/aceptar2ndchance') . '?pid=0&cid=0&t=preview',
		);
		$html = $this->load->view('email/invitacion_2ndchance.php', $data, true);
		$this->protect->ajaxDie(array('action'=>true, 'html'=>$html));
	}

	public function enviarInvitacion2ndChanceIndividual() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$partner_id = intval($this->input->post('partner_id'));
		$secret     = 'baltc_2ndchance_2026';
		$orden_rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');

		$partner = $this->User->getById($partner_id);
		if(!$partner) $this->protect->ajaxDie(array('action'=>false,'msg'=>'Jugador no encontrado.'));
		if(!filter_var($partner->email, FILTER_VALIDATE_EMAIL)) $this->protect->ajaxDie(array('action'=>false,'msg'=>'El jugador no tiene email válido.'));

		// Buscar su categoría original (no 2nd chance) y ronda inicial
		$reserva = $this->db->query("
			SELECT r.category, c.name AS cat_name
			FROM reservations r
			JOIN reservations_partners rp ON rp.reservation_id = r.id
			JOIN category c ON c.id = r.category
			WHERE rp.partner_id = ? AND c.name NOT LIKE '%2nd chance%'
			LIMIT 1
		", array($partner_id))->row();

		if(!$reserva) $this->protect->ajaxDie(array('action'=>false,'msg'=>'El jugador no tiene categoría original asignada.'));

		$ronda_inicial = null;
		foreach($orden_rondas as $ronda) {
			$r = $this->db->query(
				"SELECT COUNT(*) AS cnt FROM matches WHERE category=? AND ronda=? AND ganador_id IS NOT NULL AND score!='BYE'",
				array($reserva->category, $ronda)
			)->row();
			if($r->cnt > 0) { $ronda_inicial = $ronda; break; }
		}
		if(!$ronda_inicial) $this->protect->ajaxDie(array('action'=>false,'msg'=>'No se encontró ronda inicial jugada para su categoría.'));

		$cat_2nd = $this->db->where('name', $reserva->cat_name . ' 2nd chance')->get('category')->row();
		if(!$cat_2nd) $this->protect->ajaxDie(array('action'=>false,'msg'=>'No existe la categoría "' . $reserva->cat_name . ' 2nd chance".'));

		$token = md5($partner_id . ':' . $cat_2nd->id . ':' . $secret);
		$link  = base_url('invitacion/aceptar2ndchance') . '?pid=' . $partner_id . '&cid=' . $cat_2nd->id . '&t=' . $token;

		$data = array(
			'nombre'           => $partner->name,
			'categoria'        => $reserva->cat_name,
			'ronda_inicial'    => $ronda_inicial,
			'link_inscripcion' => $link,
		);
		$body = $this->load->view('email/invitacion_2ndchance.php', $data, true);
		$this->load->library('email');
		$this->email->initialize(array());
		$this->email
			->from('secretaria@baltc.net', 'Secretaría BALTC')
			->to($partner->email)
			->subject('¿Querés seguir en el torneo? — 2nd Chance BALTC')
			->message($body)
			->send();

		$this->protect->ajaxDie(array('action'=>true, 'msg'=>'Mail enviado a ' . $partner->email));
	}

	public function verPendientes2ndChance() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		if(!$this->db->table_exists('mail_pendientes_2ndchance')) {
			$this->protect->ajaxDie(array('action'=>false, 'msg'=>'No hay tabla de pendientes.'));
		}

		$pendientes = $this->db->get('mail_pendientes_2ndchance')->result();
		$lista = array();
		foreach($pendientes as $p) {
			$lista[] = array(
				'id' => $p->partner_id,
				'name' => $p->partner_name,
				'email' => $p->partner_email,
				'categoria' => $p->categoria
			);
		}

		$this->protect->ajaxDie(array('action'=>true, 'pendientes'=>$lista));
	}

	public function guardarPendientes2ndChance() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$destinatarios = json_decode($this->input->post('destinatarios'), true);
		if(!is_array($destinatarios) || empty($destinatarios)) {
			$this->protect->ajaxDie(array('action'=>false, 'msg'=>'Sin destinatarios.'));
		}

		$this->db->delete('mail_pendientes_2ndchance');

		foreach($destinatarios as $d) {
			$this->db->insert('mail_pendientes_2ndchance', array(
				'partner_id' => intval($d['id']),
				'partner_name' => $d['name'],
				'partner_email' => $d['email'],
				'categoria' => isset($d['categoria']) ? $d['categoria'] : ''
			));
		}

		$this->protect->ajaxDie(array('action'=>true, 'total'=>count($destinatarios)));
	}

	public function excluirDel2ndChance() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$dni = $this->input->post('dni', true);
		if(!$dni) $this->protect->ajaxDie(array('action'=>false, 'msg'=>'DNI requerido.'));

		// Buscar partner por DNI
		$partner = $this->db->where('dni', $dni)->get('partners')->row();
		if(!$partner) $this->protect->ajaxDie(array('action'=>false, 'msg'=>'Partner no encontrado.'));

		// Crear tabla si no existe
		if(!$this->db->table_exists('mail_exclusiones_2ndchance')) {
			$this->db->query("
				CREATE TABLE mail_exclusiones_2ndchance (
					id INT AUTO_INCREMENT PRIMARY KEY,
					partner_id INT NOT NULL,
					partner_name VARCHAR(255),
					partner_dni VARCHAR(20),
					created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					UNIQUE KEY partner_id (partner_id)
				)
			");
		}

		// Verificar si ya está excluido
		$ya_excluido = $this->db->where('partner_id', $partner->id)->get('mail_exclusiones_2ndchance')->row();
		if($ya_excluido) {
			$this->protect->ajaxDie(array('action'=>false, 'msg'=>'Este partner ya está excluido.'));
		}

		// Agregar a exclusiones
		$this->db->insert('mail_exclusiones_2ndchance', array(
			'partner_id' => $partner->id,
			'partner_name' => $partner->name,
			'partner_dni' => $partner->dni
		));

		$this->protect->ajaxDie(array('action'=>true, 'msg'=>$partner->name . ' ha sido excluido del 2nd Chance.'));
	}

	public function enviar2ndChance() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$tipo = $this->input->post('tipo', true);
		$partner_id = intval($this->input->post('partner_id'));
		$secret = 'baltc_2ndchance_2026';
		$orden_rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');

		$this->load->library('email');
		$enviados = 0;

		if($tipo === 'todos') {
			// Verificar si hay pendientes guardados
			$pendientes = $this->db->get('mail_pendientes_2ndchance')->result();
			if($pendientes) {
				$rows = $pendientes;
			} else {
				// Si no hay pendientes, buscar en categorías actuales
				$rows = $this->db->query("
					SELECT DISTINCT p.id, p.name, p.email
					FROM reservations r
					JOIN reservations_partners rp ON rp.reservation_id = r.id
					JOIN partners p ON p.id = rp.partner_id
					JOIN category c ON c.id = r.category
					WHERE c.name LIKE '%2nd chance%'
					ORDER BY p.name ASC
				")->result();
			}

			// Obtener lista de excluidos
			$excluidos = array();
			if($this->db->table_exists('mail_exclusiones_2ndchance')) {
				$exc = $this->db->get('mail_exclusiones_2ndchance')->result();
				foreach($exc as $e) {
					$excluidos[] = $e->partner_id;
				}
			}

			foreach($rows as $partner) {
				$partner_id = isset($partner->partner_id) ? $partner->partner_id : $partner->id;
				// Saltar si está excluido
				if(in_array($partner_id, $excluidos)) continue;
				$email = isset($partner->email) ? $partner->email : (isset($partner->partner_email) ? $partner->partner_email : null);
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

				$partner_name = isset($partner->name) ? $partner->name : $partner->partner_name;
				$categoria_original = isset($partner->categoria) ? $partner->categoria : null;

				// Si viene de pendientes, ya tiene categoría; si no, buscar
				if(!$categoria_original) {
					$reserva = $this->db->query("
						SELECT r.category, c.name AS cat_name
						FROM reservations r
						JOIN reservations_partners rp ON rp.reservation_id = r.id
						JOIN category c ON c.id = r.category
						WHERE rp.partner_id = ? AND c.name NOT LIKE '%2nd chance%'
						LIMIT 1
					", array($partner_id))->row();
					if(!$reserva) continue;
					$categoria_original = $reserva->cat_name;
				}

				// Obtener categoría 2nd chance
				$cat_2nd = $this->db->where('name', $categoria_original . ' 2nd chance')->get('category')->row();
				if(!$cat_2nd) continue;

				// Obtener ronda inicial
				$ronda_inicial = null;
				$cat_original = $this->db->where('name', $categoria_original)->get('category')->row();
				if($cat_original) {
					foreach($orden_rondas as $ronda) {
						$r = $this->db->query(
							"SELECT COUNT(*) AS cnt FROM matches WHERE category=? AND ronda=? AND ganador_id IS NOT NULL AND score!='BYE'",
							array($cat_original->id, $ronda)
						)->row();
						if($r->cnt > 0) { $ronda_inicial = $ronda; break; }
					}
				}
				if(!$ronda_inicial) $ronda_inicial = '1ra Ronda'; // Default

				$token = md5($partner_id . ':' . $cat_2nd->id . ':' . $secret);
				$link = base_url('invitacion/aceptar2ndchance') . '?pid=' . $partner_id . '&cid=' . $cat_2nd->id . '&t=' . $token;

				$data = array(
					'nombre' => $partner_name,
					'categoria' => $categoria_original,
					'ronda_inicial' => $ronda_inicial,
					'link_inscripcion' => $link,
				);

				$body = $this->load->view('email/invitacion_2ndchance.php', $data, true);
				$this->email->initialize(array());
				$this->email
					->from('secretaria@baltc.net', 'Secretaría BALTC')
					->to($email)
					->subject('¿Querés seguir en el torneo? — 2nd Chance BALTC')
					->message($body)
					->send();

				$enviados++;
			}
		} elseif($tipo === 'individual' && $partner_id) {
			$partner = $this->User->getById($partner_id);
			if(!$partner || !filter_var($partner->email, FILTER_VALIDATE_EMAIL)) {
				$this->protect->ajaxDie(array('action'=>false, 'msg'=>'Jugador o email no válido.'));
			}

			$reserva = $this->db->query("
				SELECT r.category, c.name AS cat_name
				FROM reservations r
				JOIN reservations_partners rp ON rp.reservation_id = r.id
				JOIN category c ON c.id = r.category
				WHERE rp.partner_id = ? AND c.name NOT LIKE '%2nd chance%'
				LIMIT 1
			", array($partner_id))->row();

			if(!$reserva) {
				$this->protect->ajaxDie(array('action'=>false, 'msg'=>'El jugador no tiene categoría original.'));
			}

			$ronda_inicial = null;
			foreach($orden_rondas as $ronda) {
				$r = $this->db->query(
					"SELECT COUNT(*) AS cnt FROM matches WHERE category=? AND ronda=? AND ganador_id IS NOT NULL AND score!='BYE'",
					array($reserva->category, $ronda)
				)->row();
				if($r->cnt > 0) { $ronda_inicial = $ronda; break; }
			}

			if(!$ronda_inicial) {
				$this->protect->ajaxDie(array('action'=>false, 'msg'=>'No hay ronda inicial jugada para esta categoría.'));
			}

			$cat_2nd = $this->db->where('name', $reserva->cat_name . ' 2nd chance')->get('category')->row();
			if(!$cat_2nd) {
				$this->protect->ajaxDie(array('action'=>false, 'msg'=>'No existe categoría 2nd chance.'));
			}

			$token = md5($partner_id . ':' . $cat_2nd->id . ':' . $secret);
			$link = base_url('invitacion/aceptar2ndchance') . '?pid=' . $partner_id . '&cid=' . $cat_2nd->id . '&t=' . $token;

			$data = array(
				'nombre' => $partner->name,
				'categoria' => $reserva->cat_name,
				'ronda_inicial' => $ronda_inicial,
				'link_inscripcion' => $link,
			);

			$body = $this->load->view('email/invitacion_2ndchance.php', $data, true);
			$this->email->initialize(array());
			$this->email
				->from('secretaria@baltc.net', 'Secretaría BALTC')
				->to($partner->email)
				->subject('¿Querés seguir en el torneo? — 2nd Chance BALTC')
				->message($body)
				->send();

			$enviados = 1;
		}

		$this->protect->ajaxDie(array('action'=>true, 'enviados'=>$enviados));
	}

	public function get2ndChanceInscriptos() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$rows = $this->db->query("
			SELECT p.id, p.name, p.gender, r.id AS reserva_id, c.id AS cat_id, c.name AS categoria
			FROM reservations r
			JOIN reservations_partners rp ON rp.reservation_id = r.id
			JOIN partners p ON p.id = rp.partner_id
			JOIN category c ON c.id = r.category
			WHERE c.name LIKE '%2nd chance%'
			ORDER BY c.name ASC, p.name ASC
		")->result();
		$this->protect->ajaxDie(array('action'=>true, 'inscriptos'=> $rows ?: array()));
	}

	public function mails() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']    = 'Envío de Mails';
		$d['token']     = $this->protect->eToken();
		$d['section']   = 'admin-mails';
		$d['readonly']  = $this->Administrator->isReadOnly();
		$d['categories'] = $this->Administrator->getAllCategories();
		$this->load->view('admin/header', $d);
		$this->load->view('admin/mails');
		$this->load->view('admin/footer');
	}

	public function mails2ndchance() {
		$this->protect->setRequest('GET');
		if (!$this->Administrator->isLogged()) redirect(base_url('/admin'));
		$d['titulo']    = 'Invitación 2nd Chance';
		$d['token']     = $this->protect->eToken();
		$d['section']   = 'admin-mails-2ndchance';
		$d['readonly']  = $this->Administrator->isReadOnly();
		$this->load->view('admin/header', $d);
		$this->load->view('admin/mails_2ndchance');
		$this->load->view('admin/footer');
	}

	public function getDestinatariosNotificacion() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$tipo       = $this->input->post('tipo', true);
		$category   = intval($this->input->post('category'));
		$partner_id = intval($this->input->post('partner_id'));
		$jugadores  = array();
		if($tipo === 'todos') {
			$jugadores = $this->Administrator->getJugadoresByTournament(false, false, 'doubles');
		} elseif($tipo === 'categoria' && $category) {
			$jugadores = $this->Administrator->getJugadoresByCategory($category);
		} elseif($tipo === 'individual' && $partner_id) {
			$j = $this->User->getById($partner_id);
			if($j) $jugadores = array($j);
		}
		$destinatarios = array();
		$seen = array();
		foreach($jugadores as $j) {
			if(in_array($j->id, $seen)) continue;
			if(!filter_var($j->email, FILTER_VALIDATE_EMAIL)) continue;
			$seen[] = $j->id;
			$destinatarios[] = array(
				'id'       => $j->id,
				'name'     => $j->name,
				'email'    => $j->email,
				'categoria'=> isset($j->categoria) ? $j->categoria : '',
			);
		}
		$this->protect->ajaxDie(array('action'=>true, 'total'=>count($destinatarios), 'destinatarios'=>$destinatarios));
	}

public function enviarNotificacion() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));
		$tipo        = $this->input->post('tipo', true);
		$category    = intval($this->input->post('category'));
		$partner_id  = intval($this->input->post('partner_id'));
		$template_id = $this->input->post('template_id', true);
		$templates   = $this->_getEmailTemplates();
		if(empty($template_id) || !isset($templates[$template_id])) {
			$this->protect->ajaxDie(array('action'=>false, 'msg'=>'Template no válido.'));
		}
		$asunto = $templates[$template_id]['asunto'];
		$jugadores = array();
		if($tipo === 'todos') {
			$jugadores = $this->Administrator->getJugadoresByTournament(false, false, 'doubles');
		} elseif($tipo === 'categoria' && $category) {
			$jugadores = $this->Administrator->getJugadoresByCategory($category);
		} elseif($tipo === 'individual' && $partner_id) {
			$j = $this->User->getById($partner_id);
			if($j) $jugadores = array($j);
		}
		$this->load->library('email');
		$enviados = 0;
		$seen = array();
		foreach($jugadores as $j) {
			if(in_array($j->id, $seen)) continue;
			if(!filter_var($j->email, FILTER_VALIDATE_EMAIL)) continue;
			$seen[] = $j->id;
			$data = array('nombre' => $j->name);
			$body = $this->load->view('email/' . $template_id . '.php', $data, true);
			$this->email->initialize(array());
			$this->email
				->from('secretaria@baltc.net', 'Secretaría BALTC')
				->to($j->email)
				->subject($asunto)
				->message($body)
				->send();
			$enviados++;
		}
		$this->protect->ajaxDie(array('action'=>true, 'enviados'=>$enviados));
	}

	public function descargarExcelJugadores() {
		if (!$this->Administrator->isLogged()) {
			redirect('admin/login');
		}

		$tournament_type = $this->input->post('tournament_type', true) ?: 'doubles';

		$sql = "SELECT
					c.name AS categoria,
					GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ' - ') AS pareja,
					p.gender,
					r.id
				FROM reservations r
				JOIN reservations_partners rp ON r.id = rp.reservation_id
				JOIN partners p ON p.id = rp.partner_id
				JOIN category c ON c.id = r.category
				WHERE r.tournament_type = '" . $this->db->escape_str($tournament_type) . "'
				GROUP BY r.id
				ORDER BY c.name, pareja";

		$query  = $this->db->query($sql);
		$rows   = $query->result();

		$html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
		$html .= '<head><meta charset="UTF-8"></head><body>';
		$html .= '<table border="1">';
		$html .= '<tr>
					<th>Pareja</th>
					<th>Categoría</th>
					<th>Género</th>
				  </tr>';

		foreach ($rows as $row) {
			$genero = ($row->gender === 'M') ? 'Caballeros' : 'Damas';
			$html .= '<tr>';
			$html .= '<td>' . htmlspecialchars($row->pareja, ENT_QUOTES, 'UTF-8') . '</td>';
			$html .= '<td>' . htmlspecialchars($row->categoria, ENT_QUOTES, 'UTF-8') . '</td>';
			$html .= '<td>' . $genero . '</td>';
			$html .= '</tr>';
		}

		$html .= '</table></body></html>';

		header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
		header('Content-Disposition: attachment; filename="parejas_dobles_' . date('Y-m-d') . '.xls"');
		header('Pragma: no-cache');
		header('Expires: 0');

		echo "\xEF\xBB\xBF" . $html;
		exit;
	}

	public function ranking() {
		$this->protect->setRequest('GET');
		$d['titulo'] = 'Ranking de Jugadores';
		$d['token'] = $this->protect->eToken();
		$d['section'] = 'admin-ranking';
		$this->load->view('admin/header', $d);
		$this->load->view('admin/ranking');
		$this->load->view('admin/footer');
	}

	public function getRankingData() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		$this->load->model('Ranking_model');
		$gender = $this->input->post('gender');
		$ranking = $this->Ranking_model->getRankingByGender($gender);
		$detailed = $this->Ranking_model->getRankingDetailedByGender($gender);
		$this->protect->ajaxDie(array(
			'action' => true,
			'ranking' => $ranking,
			'detailed' => $detailed
		));
	}

	public function debugRanking() {
		$this->load->model('Ranking_model');
		$debug = $this->Ranking_model->getDebugInfo();
		echo '<pre>';
		print_r($debug);
		echo '</pre>';
	}

	public function descargarRankingPDF() {
		$this->protect->setRequest('GET');
		$this->load->model('Ranking_model');

		$ranking_M = $this->Ranking_model->getRankingByGender('M');
		$ranking_F = $this->Ranking_model->getRankingByGender('F');

		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<title>Ranking de Jugadores BALTC</title>
			<style>
				body {
					font-family: Arial, sans-serif;
					max-width: 900px;
					margin: 20px auto;
					padding: 20px;
					color: #333;
					line-height: 1.6;
				}
				.header {
					text-align: center;
					margin-bottom: 25px;
					border-bottom: 3px solid #0066cc;
					padding-bottom: 20px;
				}
				.logo {
					max-width: 120px;
					height: auto;
					margin-bottom: 15px;
				}
				h1 {
					color: #0066cc;
					font-size: 28px;
					margin: 10px 0;
				}
				.subtitle {
					color: #666;
					font-size: 14px;
					margin: 5px 0;
				}
				.description-box {
					background: #f0f7ff;
					border-left: 3px solid #0066cc;
					padding: 12px;
					margin: 20px 0;
					font-size: 13px;
					line-height: 1.5;
				}
				.description-box strong {
					display: block;
					margin-bottom: 8px;
					color: #0066cc;
				}
				.punto {
					margin: 6px 0;
					padding-left: 15px;
				}
				.section {
					margin: 25px 0;
					page-break-inside: avoid;
				}
				.section h2 {
					color: #0066cc;
					font-size: 18px;
					margin: 15px 0 10px 0;
					border-bottom: 2px solid #0066cc;
					padding-bottom: 5px;
				}
				table {
					width: 100%;
					border-collapse: collapse;
					margin: 15px 0;
					font-size: 12px;
				}
				th, td {
					padding: 8px;
					text-align: left;
					border-bottom: 1px solid #ddd;
				}
				th {
					background: #0066cc;
					color: white;
					font-weight: bold;
				}
				tr:nth-child(even) {
					background: #f9f9f9;
				}
				.pos-1 { background: #fffacd !important; }
				.pos-2 { background: #f0f0f0 !important; }
				.pos-3 { background: #ffe8d6 !important; }
				.footer {
					text-align: center;
					margin-top: 40px;
					padding-top: 20px;
					border-top: 1px solid #ddd;
					color: #999;
					font-size: 12px;
				}
				.print-note {
					background: #e7f3ff;
					border: 1px solid #0066cc;
					padding: 12px;
					border-radius: 5px;
					margin-bottom: 20px;
					text-align: center;
					font-size: 12px;
				}
				@media print {
					body { margin: 0; padding: 10px; }
					.print-note { display: none; }
				}
			</style>
		</head>
		<body>
			<div class="print-note">
				<strong>💡 Para descargar como PDF:</strong> Presiona <strong>Ctrl+P</strong> (o <strong>Cmd+P</strong> en Mac) y selecciona "Guardar como PDF"
			</div>

			<div class="header">
				<img src="<?=asset_url('img/logo.png')?>" alt="BALTC" class="logo">
				<h1>🏆 Ranking de Jugadores</h1>
				<p class="subtitle">Posiciones basadas en victorias ponderadas por ronda y categoría</p>
			</div>

			<div class="description-box">
				<strong>Sistema de puntuación con movilidad entre categorías:</strong>
				<div class="punto">• <strong>Puntos base:</strong> 1ra +270 | 2da +150 | 3era +100</div>
				<div class="punto">• <strong>Fórmula:</strong> (puntos_ronda × multiplicador) ÷ divisor</div>
				<div class="punto">• <strong>Ejemplo Final (80 pts):</strong> 1ra: 240÷1=240 | 2da: 160÷1.3=123 | 3era: 80÷4.8=17</div>
				<div class="punto">• <strong>Movilidad garantizada:</strong> Finalista 2da (273) > peor 1ra (270) ✓ | Mejor 3era máx (267) < peor 1ra (270) ✓ | 3era nunca salta 2 categorías</div>
			</div>

			<div class="section">
				<h2>👨 Caballeros</h2>
				<?php if(!empty($ranking_M)): ?>
				<table>
					<thead>
						<tr>
							<th style="width:40px;">Pos</th>
							<th style="width:50px;">Cat</th>
							<th>Jugador</th>
							<th style="width:70px;">Puntos</th>
							<th style="width:60px;">Victorias</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($ranking_M as $idx => $r):
							$pos = $idx + 1;
							$posClass = 'pos-' . $pos;
						?>
						<tr class="<?=$posClass?>">
							<td style="text-align:center; font-weight:bold;"><?=$pos?></td>
							<td style="text-align:center; font-size:11px;">
								<?php
									$cat = $r['categoria'];
									if(stripos($cat, '1') !== false) echo '1ra';
									elseif(stripos($cat, '2') !== false) echo '2da';
									elseif(stripos($cat, '3') !== false) echo '3era';
									else echo $cat;
								?>
							</td>
							<td style="text-transform:capitalize;"><?=strtolower($r['name'])?></td>
							<td style="text-align:center; font-weight:bold;"><?=$r['puntos']?></td>
							<td style="text-align:center;"><?=$r['victorias']?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php else: ?>
				<p><em>Sin datos disponibles</em></p>
				<?php endif; ?>
			</div>

			<div class="section">
				<h2>👩 Damas</h2>
				<?php if(!empty($ranking_F)): ?>
				<table>
					<thead>
						<tr>
							<th style="width:40px;">Pos</th>
							<th style="width:50px;">Cat</th>
							<th>Jugador</th>
							<th style="width:70px;">Puntos</th>
							<th style="width:60px;">Victorias</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($ranking_F as $idx => $r):
							$pos = $idx + 1;
							$posClass = 'pos-' . $pos;
						?>
						<tr class="<?=$posClass?>">
							<td style="text-align:center; font-weight:bold;"><?=$pos?></td>
							<td style="text-align:center; font-size:11px;">
								<?php
									$cat = $r['categoria'];
									if(stripos($cat, '1') !== false) echo '1ra';
									elseif(stripos($cat, '2') !== false) echo '2da';
									elseif(stripos($cat, '3') !== false) echo '3era';
									else echo $cat;
								?>
							</td>
							<td style="text-transform:capitalize;"><?=strtolower($r['name'])?></td>
							<td style="text-align:center; font-weight:bold;"><?=$r['puntos']?></td>
							<td style="text-align:center;"><?=$r['victorias']?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php else: ?>
				<p><em>Sin datos disponibles</em></p>
				<?php endif; ?>
			</div>

			<div class="footer">
				<p><strong>BALTC - Ranking de Jugadores</strong></p>
				<p style="font-size: 11px; margin-top: 10px;">Generado el <?=date('d/m/Y a las H:i')?> - Sistema de Ranking Jerárquico</p>
			</div>
		</body>
		</html>
		<?php
	}

	public function debugSeeding() {
		echo "<strong>Columnas en tabla 'sembrados':</strong><br>";
		$col_sem = $this->db->query("SHOW COLUMNS FROM sembrados");
		foreach($col_sem->result() as $col) {
			echo "- " . $col->Field . " (" . $col->Type . ")<br>";
		}

		echo "<br><strong>Ejemplos de datos en sembrados:</strong><br>";
		$sql = "SELECT * FROM sembrados LIMIT 10";
		$q = $this->db->query($sql);
		echo '<pre>';
		print_r($q->result());
		echo '</pre>';

		echo "<br><strong>Estructura completa con JOIN:</strong><br>";
		$sql = "SELECT s.*, p.name, c.name as categoria
				FROM sembrados s
				LEFT JOIN partners p ON p.id = s.partner_id
				LEFT JOIN category c ON c.id = s.category_id
				LIMIT 5";
		$q = $this->db->query($sql);
		echo '<pre>';
		print_r($q->result());
		echo '</pre>';
	}

	public function getPartnersForPairing() {
		header('Content-Type: application/json');

		// Obtener todos los partners (sin filtrar por status)
		$partners_result = $this->db->get('partners')->result();
		$partners = array();
		foreach($partners_result as $p) {
			$partners[] = array(
				'id' => $p->id,
				'name' => $p->name,
				'dni' => $p->dni,
				'gender' => $p->gender
			);
		}

		// Obtener categorías de dobles
		$categories = $this->Reservation->getCategories(null, true);

		$response = array(
			'action' => true,
			'partners' => $partners,
			'categories' => $categories
		);

		echo json_encode($response);
		exit;
	}

	public function addPairing() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if (!$this->Administrator->isLogged()) {
			$this->protect->ajaxDie(array('action' => false));
		}

		$gender = $this->input->post('gender', true);
		$category = $this->input->post('category', true);
		$player1 = $this->input->post('player1', true);
		$player2 = $this->input->post('player2', true);

		if (!$gender || !$category || !$player1 || !$player2) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Campos requeridos'));
		}

		if ($player1 == $player2) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Los jugadores deben ser diferentes'));
		}

		// Crear reserva
		$resData = array(
			'category' => $category,
			'tournament_type' => 'doubles'
		);

		$this->db->insert('reservations', $resData);
		$reservation_id = $this->db->insert_id();

		// Agregar partners
		$this->db->insert('reservations_partners', array('reservation_id' => $reservation_id, 'partner_id' => $player1));
		$this->db->insert('reservations_partners', array('reservation_id' => $reservation_id, 'partner_id' => $player2));

		$this->protect->ajaxDie(array('action' => true, 'msg' => 'Pareja creada correctamente'));
	}

	public function addPartnerQuick() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if (!$this->Administrator->isLogged()) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'No autorizado'));
		}

		$name = $this->input->post('name', true);
		$dni = $this->input->post('dni', true);
		$gender = $this->input->post('gender', true);
		$email = $this->input->post('email', true);

		if (!$name || !$dni || !$gender) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Campos requeridos: nombre, DNI, género'));
		}

		$result = $this->Administrator->addPartner($name, $dni, $gender, $email);
		$this->protect->ajaxDie(array('action' => $result, 'msg' => $result ? 'Partner agregado correctamente' : 'Error al agregar partner'));
	}

	public function debugBracket() {
		if(!$this->Administrator->isLogged()) redirect(base_url());

		$data = array();

		// 1. Categorías con "1ra"
		$cats = $this->db->query("SELECT id, name FROM category WHERE name LIKE '%1ra%'")->result();
		$data['categorias'] = $cats;

		// 2. Todos los Cuartos de Final
		$cuartos = $this->db->query("SELECT m.id, m.jugador1_id, m.jugador2_id, m.ronda, c.name, m.gender FROM matches m JOIN category c ON c.id = m.category WHERE m.ronda = 'Cuartos de Final' ORDER BY c.name, m.id LIMIT 50")->result();
		$data['cuartos_final'] = $cuartos;

		// 3. Partidos con Pierini
		$pierini = $this->db->query("SELECT m.id, m.jugador1_id, m.jugador2_id, m.ronda, c.name FROM matches m JOIN category c ON c.id = m.category WHERE m.jugador1_id IN (SELECT DISTINCT reservation_id FROM reservations_partners WHERE partner_id IN (SELECT id FROM partners WHERE name LIKE '%Pierini%')) OR m.jugador2_id IN (SELECT DISTINCT reservation_id FROM reservations_partners WHERE partner_id IN (SELECT id FROM partners WHERE name LIKE '%Pierini%')) ORDER BY m.ronda DESC LIMIT 30")->result();
		$data['pierini'] = $pierini;

		// 4. Partners en cada reservations para verificar si hay duplicados
		$dupcheck = $this->db->query("
			SELECT r.id, GROUP_CONCAT(p.name SEPARATOR ', ') as partners
			FROM reservations r
			LEFT JOIN reservations_partners rp ON rp.reservation_id = r.id
			LEFT JOIN partners p ON p.id = rp.partner_id
			WHERE r.id IN (194, 208, 204, 205, 240, 238, 233, 226, 230, 235, 218)
			GROUP BY r.id
		")->result();
		$data['dupcheck'] = $dupcheck;

		$this->load->view('debug_bracket', $data);
	}

	public function testResultado() {
		if(!$this->Administrator->isLogged()) redirect(base_url());

		// Obtener todos los partidos sin resultado
		$partidos = $this->db->query("
			SELECT m.id, m.jugador1_id, m.jugador2_id, m.ronda, c.name as categoria, m.gender,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador1_id) as j1_name,
				(SELECT GROUP_CONCAT(p.name SEPARATOR ' / ')
					FROM reservations_partners rp
					JOIN partners p ON p.id = rp.partner_id
					WHERE rp.reservation_id = m.jugador2_id) as j2_name
			FROM matches m
			JOIN category c ON c.id = m.category
			WHERE m.ganador_id IS NULL AND m.score IS NULL
			ORDER BY c.name, m.ronda, m.id
			LIMIT 50
		")->result();

		$d['token'] = $this->protect->eToken();
		$d['partidos'] = $partidos;
		$this->load->view('test_resultado', $d);
	}

	public function revertirResultado() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$partido_id = intval($this->input->post('id'));
		$partido = $this->Partido_model->getById($partido_id);
		if(!$partido) $this->protect->ajaxDie(array('action'=>false, 'msg'=>'Partido no encontrado'));

		// Limpiar resultado
		$this->Partido_model->edit($partido_id, array('score' => null, 'ganador_id' => null));

		// Eliminar partidos de rondas posteriores que dependían de este
		$rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');
		$ronda_idx = array_search($partido->ronda, $rondas);
		if($ronda_idx !== false && $ronda_idx < count($rondas) - 1) {
			$siguiente_ronda = $rondas[$ronda_idx + 1];
			$bp = intval($partido->bracket_pos);
			$new_bp = (int)floor($bp / 2);
			$this->db->delete('matches', array(
				'category' => $partido->category,
				'gender' => $partido->gender,
				'ronda' => $siguiente_ronda,
				'bracket_pos' => $new_bp
			));
		}

		$this->protect->ajaxDie(array('action'=>true, 'msg'=>'Resultado revertido correctamente'));
	}

	public function testCargarResultado() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->Administrator->isLogged()) $this->protect->ajaxDie(array('action'=>false));

		$partido_id = intval($this->input->post('id'));
		$ganador_id = intval($this->input->post('ganador_id'));
		$score = $this->input->post('score', true);
		$test_email = $this->input->post('test_email', true);

		$partido = $this->Partido_model->getById($partido_id);
		if(!$partido) $this->protect->ajaxDie(array('action'=>false, 'msg'=>'Partido no encontrado'));
		if(!empty($partido->ganador_id)) $this->protect->ajaxDie(array('action'=>false, 'msg'=>'Partido ya tiene resultado'));

		$data = array('score' => $score, 'ganador_id' => $ganador_id);
		$updated = $this->Partido_model->edit($partido_id, $data);

		if($updated) {
			// Avanzar bracket
			$rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');
			$ronda_idx = array_search($partido->ronda, $rondas);
			if($ronda_idx !== false && $ronda_idx < count($rondas) - 1) {
				$siguiente_ronda = $rondas[$ronda_idx + 1];
				$partidos_ronda = $this->Partido_model->getByRonda($partido->category, $partido->gender, $partido->ronda);
				if($partidos_ronda) {
					$bp = intval($partido->bracket_pos);
					$hermano_bp = ($bp % 2 === 0) ? $bp + 1 : $bp - 1;
					$hermano = null;
					foreach($partidos_ronda as $p) {
						if(intval($p->bracket_pos) === $hermano_bp) { $hermano = $p; break; }
					}
					if($hermano) {
						// Si hermano es BYE, usar jugador1_id; si no, usar ganador_id
						$hermano_ganador = ($hermano->score === 'BYE') ? $hermano->jugador1_id : $hermano->ganador_id;
						if(!empty($hermano_ganador)) {
							if($bp % 2 === 0) {
								$j1 = $ganador_id;
								$j2 = intval($hermano_ganador);
							} else {
								$j1 = intval($hermano_ganador);
								$j2 = $ganador_id;
							}
							$existe = $this->Partido_model->existePartido($partido->category, $partido->gender, $siguiente_ronda, $j1, $j2);
							if(!$existe) {
								$this->Partido_model->add(array(
									'category' => $partido->category,
									'gender' => $partido->gender ?: '',
									'ronda' => $siguiente_ronda,
									'bracket_pos' => (int)floor(min($bp, $hermano_bp) / 2),
									'jugador1_id' => $j1,
									'jugador2_id' => $j2,
									'score' => null,
									'ganador_id' => null
								));
							}
						}
					}
				}
			}

			// Enviar email de resultado a test_email
			if($test_email && filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
				$perdedor_id = $partido->jugador1_id == $ganador_id ? $partido->jugador2_id : $partido->jugador1_id;

				// Detectar si es dobles o singles
				$ganador_partners = $this->db->query(
					"SELECT p.name FROM reservations_partners rp
					 JOIN partners p ON p.id = rp.partner_id
					 WHERE rp.reservation_id = ?
					 ORDER BY p.name ASC",
					array($ganador_id)
				)->result();

				if(!empty($ganador_partners)) {
					// Dobles
					$ganador_name = implode(' / ', array_map(function($x) { return $x->name; }, $ganador_partners));
					$perdedor_partners = $this->db->query(
						"SELECT p.name FROM reservations_partners rp
						 JOIN partners p ON p.id = rp.partner_id
						 WHERE rp.reservation_id = ?
						 ORDER BY p.name ASC",
						array($perdedor_id)
					)->result();
					$perdedor_name = !empty($perdedor_partners) ? implode(' / ', array_map(function($x) { return $x->name; }, $perdedor_partners)) : '?';
				} else {
					// Singles
					$ganador_user = $this->User->getById($ganador_id);
					if(!$ganador_user) return;
					$ganador_name = $ganador_user->name;
					$perdedor_user = $this->User->getById($perdedor_id);
					$perdedor_name = $perdedor_user ? $perdedor_user->name : '?';
				}

				$this->load->library('email');
				$email_data = array(
					'nombre' => $ganador_name,
					'ganador' => $ganador_name,
					'perdedor' => $perdedor_name,
					'categoria' => $partido->categoria,
					'ronda' => $partido->ronda,
					'score' => $score
				);
				$body = $this->load->view('email/resultado_confirm.php', $email_data, true);
				$this->email->initialize(array());
				$this->email
					->from('secretaria@baltc.net', 'Secretaría BALTC')
					->to($test_email)
					->subject('Resultado de tu partido - Torneo BALTC (TEST)')
					->message($body)
					->send();
			}
		}

		$this->protect->ajaxDie(array('action'=>$updated !== false, 'msg'=>$updated ? 'Resultado cargado correctamente' : 'Error al cargar resultado'));
	}
}
