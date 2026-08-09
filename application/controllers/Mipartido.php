<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mipartido extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
		$this->load->model('Partido_model');
		$this->load->model('Reservation');
	}

	public function index() {
		$this->protect->setRequest('GET');
		if(!$this->User->isLogged()) redirect(base_url());

		$user_id = $this->session->userdata('id');

		$d['titulo']    = 'Mi Partido';
		$d['token']     = $this->protect->eToken();
		$d['user']      = $this->session;
		$d['classname'] = 'reserva';
		$d['partidos']  = $this->Partido_model->getPendientesByPlayer($user_id);

		$this->load->view('web/header', $d);
		$this->load->view('web/mipartido');
		$this->load->view('web/footer');
	}

	public function cargarResultado() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');

		if(!$this->User->isLogged()) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'No autorizado.'));
		}

		$partner_id  = $this->session->userdata('id');
		$id       = intval($this->input->post('id'));
		$score    = $this->input->post('score', true);
		$ganador  = intval($this->input->post('ganador_id'));
		$test_email = $this->input->post('test_email', true);

		// Obtener el partido
		$partido = $this->Partido_model->getById($id);
		if(!$partido) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Partido no encontrado.'));
		}

		// El usuario debe ser quien reporta el resultado - puede ser jugador1 o jugador2
		// Para partidos de grupo (dobles), jugador1_id y jugador2_id son reservation_ids
		// Para partidos normales (singles), pueden ser partner_ids

		// Intentar verificar directamente con partner_id primero (para singles)
		$user_is_jugador = ($partido->jugador1_id == $partner_id || $partido->jugador2_id == $partner_id);

		// Si no encontró como partner, buscar como reservation
		if(!$user_is_jugador) {
			$res_q = $this->db->query(
				"SELECT DISTINCT r.id FROM reservations r
				 JOIN reservations_partners rp ON rp.reservation_id = r.id
				 WHERE rp.partner_id = ?",
				array($partner_id)
			)->result();

			foreach($res_q as $r) {
				if($partido->jugador1_id == $r->id || $partido->jugador2_id == $r->id) {
					$user_is_jugador = true;
					$partner_id = $r->id; // Usar reservation_id para el resto
					break;
				}
			}
		}

		if(!$user_is_jugador) {
			error_log("DEBUG: usuario no es jugador. partner_id=$partner_id, j1={$partido->jugador1_id}, j2={$partido->jugador2_id}");
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'No tenés permiso para cargar este resultado.'));
		}

		// Ahora partner_id contiene el ID correcto (partner_id o reservation_id según sea necesario)
		$user_res_id = $partner_id;
		if($ganador != $user_res_id) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Solo el ganador puede cargar el resultado.'));
		}
		if(!empty($partido->ganador_id)) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Este partido ya tiene resultado cargado.'));
		}

		$data = array('score' => $score, 'ganador_id' => $user_res_id);
		$updated = $this->Partido_model->edit($id, $data);

		// Avanzar bracket y enviar email
		if($updated !== false) {
			$this->_avanzarBracket($id, $user_res_id, $data);
			$this->_sendResultadoEmail($id, $user_res_id, $score, $test_email);
		}

		$this->protect->ajaxDie(array('action' => $updated !== false));
	}

	public function guardarFechaAcordada() {
		$this->protect->setAjax();
		$this->protect->setRequest('POST');
		if(!$this->User->isLogged()) $this->protect->ajaxDie(array('action'=>false,'msg'=>'No autorizado.'));

		$user_id = $this->session->userdata('id');
		$id      = intval($this->input->post('id'));
		$fecha   = $this->input->post('fecha', true);
		$hora    = $this->input->post('hora', true);

		$partido = $this->Partido_model->getById($id);
		if(!$partido) $this->protect->ajaxDie(array('action'=>false,'msg'=>'Partido no encontrado.'));
		if($partido->jugador1_id != $user_id && $partido->jugador2_id != $user_id) {
			$this->protect->ajaxDie(array('action'=>false,'msg'=>'Sin permiso.'));
		}
		if(!empty($partido->deadline) && !empty($fecha) && strtotime($fecha) > strtotime($partido->deadline)) {
			$this->protect->ajaxDie(array('action'=>false,'msg'=>'La fecha debe ser antes del deadline (' . date('d/m/Y', strtotime($partido->deadline)) . ').'));
		}

		$this->Partido_model->edit($id, array('fecha' => $fecha, 'hora' => $hora ?: null));
		$this->protect->ajaxDie(array('action'=>true));
	}

	private function _sendResultadoEmail($partido_id, $ganador_id, $score, $test_email = null) {
		$partido = $this->Partido_model->getById($partido_id);
		if(!$partido) return;

		$perdedor_id = $partido->jugador1_id == $ganador_id ? $partido->jugador2_id : $partido->jugador1_id;

		// INTENTA OBTENER EMAIL COMO DOBLES (reservation_id)
		$ganador_email = null;
		$ganador_name = null;

		$ganador_partners = $this->db->query(
			"SELECT p.name, p.email FROM reservations_partners rp
			 JOIN partners p ON p.id = rp.partner_id
			 WHERE rp.reservation_id = ?
			 ORDER BY p.name ASC",
			array($ganador_id)
		)->result();

		if(!empty($ganador_partners)) {
			// Es DOBLES - obtener email del primer partner
			$ganador_name = implode(' / ', array_map(function($x) { return $x->name; }, $ganador_partners));
			$ganador_email = reset($ganador_partners)->email;
		}

		// SI NO ENCONTRÓ COMO DOBLES, INTENTA COMO SINGLES (user_id/partner_id)
		if(empty($ganador_email)) {
			$ganador_user = $this->User->getById($ganador_id);
			if($ganador_user) {
				$ganador_name = $ganador_user->name;
				$ganador_email = $ganador_user->email;
			}
		}

		// SI SIGUE SIN EMAIL, NO ENVIAR
		if(empty($ganador_email) || !filter_var($ganador_email, FILTER_VALIDATE_EMAIL)) {
			error_log("CORREO FAIL: ganador_id=$ganador_id, email=$ganador_email");
			return;
		}

		// OBTENER NOMBRE DEL PERDEDOR
		$perdedor_partners = $this->db->query(
			"SELECT p.name FROM reservations_partners rp
			 JOIN partners p ON p.id = rp.partner_id
			 WHERE rp.reservation_id = ?
			 ORDER BY p.name ASC",
			array($perdedor_id)
		)->result();

		if(!empty($perdedor_partners)) {
			$perdedor_name = implode(' / ', array_map(function($x) { return $x->name; }, $perdedor_partners));
		} else {
			$perdedor_user = $this->User->getById($perdedor_id);
			$perdedor_name = $perdedor_user ? $perdedor_user->name : '?';
		}

		$email_destino = $test_email ?: $ganador_email;

		$data = array(
			'nombre'   => $ganador_name,
			'ganador'  => $ganador_name,
			'perdedor' => $perdedor_name,
			'categoria'=> $partido->categoria,
			'ronda'    => $partido->ronda,
			'score'    => $score
		);

		$this->load->library('email');
		$body = $this->load->view('email/resultado_confirm.php', $data, true);
		$this->email->initialize(array());
		$this->email
			->from('secretaria@baltc.net', 'Secretaría BALTC')
			->to($email_destino)
			->subject('Resultado de tu partido - Torneo BALTC')
			->message($body)
			->send();
	}

	private function _avanzarBracket($partido_id, $ganador_id, $post) {
		$partido = $this->Partido_model->getById($partido_id);
		if(!$partido) return;

		$rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');
		$ronda_idx = array_search($partido->ronda, $rondas);
		if($ronda_idx === false || $ronda_idx >= count($rondas) - 1) return;

		$siguiente_ronda = $rondas[$ronda_idx + 1];
		$partidos_ronda = $this->Partido_model->getByRonda($partido->category, $partido->gender, $partido->ronda);
		if(!$partidos_ronda) return;

		$bp = intval($partido->bracket_pos);
		$hermano_bp = ($bp % 2 === 0) ? $bp + 1 : $bp - 1;

		$hermano = null;
		foreach($partidos_ronda as $p) {
			if(intval($p->bracket_pos) === $hermano_bp) { $hermano = $p; break; }
		}
		if(!$hermano) {
			error_log("DEBUG: No encontró hermano. bp=$bp, hermano_bp=$hermano_bp, partidos_ronda=" . json_encode($partidos_ronda));
			return;
		}

		// Si hermano es BYE, usar jugador1_id; si no, usar ganador_id
		$hermano_ganador = ($hermano->score === 'BYE') ? $hermano->jugador1_id : $hermano->ganador_id;
		error_log("DEBUG: hermano score='{$hermano->score}', ganador_id='{$hermano->ganador_id}', jugador1_id='{$hermano->jugador1_id}', hermano_ganador=$hermano_ganador");
		if(empty($hermano_ganador)) {
			error_log("DEBUG: hermano_ganador vacío");
			return;
		}

		if($bp % 2 === 0) {
			$j1 = $ganador_id;
			$j2 = intval($hermano_ganador);
		} else {
			$j1 = intval($hermano_ganador);
			$j2 = $ganador_id;
		}

		error_log("DEBUG: Avanzando a $siguiente_ronda. j1=$j1, j2=$j2, next_pos=" . (int)floor(min($bp, $hermano_bp) / 2));

		$existe = $this->Partido_model->existePartido($partido->category, $partido->gender, $siguiente_ronda, $j1, $j2);
		if($existe) {
			error_log("DEBUG: Partido ya existe en siguiente ronda");
			return;
		}

		$gender_val = !empty($partido->gender) ? $partido->gender : '';
		$next_pos = (int)floor(min($bp, $hermano_bp) / 2);
		$result = $this->Partido_model->add(array(
			'category'    => $partido->category,
			'gender'      => $gender_val,
			'ronda'       => $siguiente_ronda,
			'bracket_pos' => $next_pos,
			'jugador1_id' => $j1,
			'jugador2_id' => $j2,
			'score'       => null,
			'ganador_id'  => null
		));
		error_log("DEBUG: Partido agregado. Result=$result");
	}
}
