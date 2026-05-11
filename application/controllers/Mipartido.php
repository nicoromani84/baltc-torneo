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

		$user_id  = $this->session->id;
		$id       = intval($this->input->post('id'));
		$score    = $this->input->post('score', true);
		$ganador  = intval($this->input->post('ganador_id'));

		// Verificar que el partido le pertenece al usuario
		$partido = $this->Partido_model->getById($id);
		if(!$partido) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Partido no encontrado.'));
		}
		if($partido->jugador1_id != $user_id && $partido->jugador2_id != $user_id) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'No tenés permiso para cargar este resultado.'));
		}
		if($ganador != $user_id) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Solo el ganador puede cargar el resultado.'));
		}
		if(!empty($partido->ganador_id)) {
			$this->protect->ajaxDie(array('action' => false, 'msg' => 'Este partido ya tiene resultado cargado.'));
		}

		$data = array('score' => $score, 'ganador_id' => $ganador);
		$updated = $this->Partido_model->edit($id, $data);

		// Avanzar bracket y enviar email
		if($updated !== false) {
			$this->_avanzarBracket($id, $ganador, $data);
			$this->_sendResultadoEmail($id, $ganador, $score);
		}

		$this->protect->ajaxDie(array('action' => $updated !== false));
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

	private function _avanzarBracket($partido_id, $ganador_id, $post) {
		$partido = $this->Partido_model->getById($partido_id);
		if(!$partido) return;

		$rondas = array('1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final');
		$ronda_idx = array_search($partido->ronda, $rondas);
		if($ronda_idx === false || $ronda_idx >= count($rondas) - 1) return;

		$siguiente_ronda = $rondas[$ronda_idx + 1];
		$partidos_ronda = $this->Partido_model->getByRonda($partido->category, $partido->gender, $partido->ronda);
		if(!$partidos_ronda) return;

		$pos = -1;
		foreach($partidos_ronda as $i => $p) {
			if($p->id == $partido_id) { $pos = $i; break; }
		}
		if($pos === -1) return;

		$hermano_pos = ($pos % 2 === 0) ? $pos + 1 : $pos - 1;
		if(!isset($partidos_ronda[$hermano_pos])) return;
		$hermano = $partidos_ronda[$hermano_pos];
		if(empty($hermano->ganador_id)) return;

		if($pos % 2 === 0) {
			$j1 = $ganador_id;
			$j2 = intval($hermano->ganador_id);
		} else {
			$j1 = intval($hermano->ganador_id);
			$j2 = $ganador_id;
		}

		$existe = $this->Partido_model->existePartido($partido->category, $partido->gender, $siguiente_ronda, $j1, $j2);
		if($existe) return;

		$gender_val = !empty($partido->gender) ? $partido->gender : '';
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
}
