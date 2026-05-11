<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * COMMON FUNCTIONS & RESOURSES
 */
class Protect extends CI_Model 
{

	/**
	 * Token Encode.
	 * devuelve un numero aleatorio codificado en md5
	 */
	public function eToken() {
		if ($this->session->custom_token)
			return $this->session->custom_token;
		$token = md5(uniqid(rand(),true));
		$this->session->set_userdata('custom_token',$token);
		return $token;
	}

	/**
	 * Token Decode.
	 * devuelve true o false dependiendo del token recibido
	 */
	public function dToken($token) {
		return ($this->session->userdata('custom_token') == $token) ? true : false;
	}



	/*
	 * ajaxTokenCheck()
	 * Chequea que el header contenga el token igual al generado
	 */
	public function ajaxTokenCheck() {
		if ( empty($_SERVER['HTTP_X_AUTH_TOKEN']) || $_SERVER['HTTP_X_AUTH_TOKEN'] != $this->session->userdata('custom_token') )
			if (ENVIRONMENT == 'production')
				redirect( base_url() );
			else
				die('Invalid Token ');
	}

	/*
	 * ajaxDie($v)
	 * $v = Mixed
	 * Mata y devuelve ajax
	 */
	public function ajaxDie($v) {
		$this->output
		->set_status_header(200)
		->set_content_type('application/json')
		->set_output(json_encode($v))
		->_display();
		die;
	}

	/**
	* setAjax().
	* Si no es ajax muere la peticion
	*/
	public function setAjax() {
		if ( empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
			if (ENVIRONMENT == 'production')
				redirect( base_url() );
			else
				die('Invalid Request, only Ajax');
	}

	/*
	* setRequest()
	* $r = String
	* Mata si el request no es valido
	*/
	public function setRequest($r) {
		if ( $_SERVER['REQUEST_METHOD'] != $r )
			if ( ENVIRONMENT == 'production' )
				redirect( base_url() );
			else
				die('Invalid Request for this method');
	}

	/*
	* getRequest()
	* Devuelve el tipo de Request
	*/
	public function getRequest() {
		return $_SERVER['REQUEST_METHOD'];
	}

	/*
	* only()
	* $role = Role
	* Muere si el usuario no es el rol que se pide
	*/
	public function only($role) {
		if ( ! in_array($role, json_decode($this->session->userdata('role'))) )
			if ( ENVIRONMENT == 'production' )
				redirect( base_url() );
			else
				die('Invalid User Role, Required '.$role);
	}

	/*
	* loggedUsers()
	* Muere si el usuario no es el rol que se pide
	*/
	public function loggedUsers() {
		if ( ! $this->session->userdata('isLogged') ) {
			if ( ENVIRONMENT == 'production' ) {
				if ( empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest' ) {
					redirect( base_url() );
				} else {
					$this->output
					->set_status_header(401)
					->set_content_type('application/json')
					->set_output()
					->_display();
					die;
				
				}
			} else {
				die('Only Logged Users');
			}
		}
	}
	


//close class
	}
