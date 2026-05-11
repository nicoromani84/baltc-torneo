<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partners extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User');
	}

	public function getAll() {
		$q = $this->User->getAll();
		$this->protect->ajaxDie(array('action' => $q !== false, 'data' => $q));
	}

	public function getAllExceptMe() {
		$q = $this->User->getAllExceptMe($this->session->gender);
		$this->protect->ajaxDie(array('action' => $q !== false, 'data' => $q));
	}

	public function getByID($id) {
		$q = $this->User->getByID($id);
		$this->protect->ajaxDie(array('action' => $q !== false, 'data' => $q));
	}
}
