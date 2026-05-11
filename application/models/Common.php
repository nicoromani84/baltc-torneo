<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * COMMON FUNCTIONS & RESOURSES
 */
class Common extends CI_Model 
{

	/*
	 * eCaptcha()
	 *
	*/
	public function eCaptcha() {
		$this->load->helper('captcha');
		$v = array(
	        'img_path'      => 'assets/img/captcha/',
	        'img_url'       => base_url().'assets/img/captcha/',
	        'pool'          => TUT_CAPTCHA_POOL,
	        'word_length'   => TUT_CAPTCHA_WLENGTH,
	        'font_size'     => 100,
	        'img_width'     => TUT_CAPTCHA_WIDTH,
	        'img_height'    => TUT_CAPTCHA_HEIGHT,
	        'colors' => array('background' => array(255, 255, 255),'border' => array(82, 190, 156),'text' => array(0, 0, 0),'grid' => array(82, 190, 156))
		);
		$c = create_captcha($v);
		$this->session->set_userdata('captcha',$c['word']);
		return $c['image'];
	}

	/**
	 * dCaptcha($c)
	 * $c = captcha generado anteriormente
	 */
	public function dCaptcha($c) {
		return ($this->session->userdata('captcha') == $c) ? true : false;
	}

	/**
	 * Time Humans.
	 * devuelve fecha y hora formateada
	 */
 	public function dateFormat($str,$cust='') {
		//pasamos el string a time
		$time = strtotime($str);
		//retornamos el time formateado
		return ($cust=='') ? date("d m Y, g:i a", $time) : date($cust, $time);
	}

	/**
	 * horArg($what).
	 * $what = DATETIME
	 * devuelve la hora formateada, GTM+3
	 */
	public function timestamp($what='') {
	    $tz_object = new DateTimeZone('Etc/GMT+3');
	    $datetime = new DateTime();
	    $datetime->setTimezone($tz_object);
	    return ($what=='') ? $datetime->format('Y-m-d\ H:i:s') : $datetime->format($what);
	}


	/**
	 * calculaedad.
	 * devuelve años en base a fecha de nacimiento
	 */
	public function calculaedad($fecha) {
	    list($Y,$m,$d) = explode("-",$fecha);
	    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
	}

	/**
	 * convierte un texto en url
	 * @input : text | @output : ascii text
	 */
	public function sToURL($s) {
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
		$clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_| -]+/", '-', $clean);
		return $clean;
	}


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////GETTERS
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Obtiene paises
	public function getPaises() {
		$this->db->order_by('nombre', 'asc');
		$q = $this->db->get('paises');
		foreach ($q->result() as $key => $value) {
			$res[] = $value->nombre;
		}
		return $q->result_array();
	}

	// Obtiene Provincias
	public function getProvincias() {
		$q = $this->db->get('provincias');
		foreach ($q->result() as $key => $value) {
			$res[] = $value->provincia;
		}
		return $q->result_array();
	}

	//Obtiene Localidades
	public function getLocalidades($provincia = false) {
		$this->db->select('l.*');
		if($provincia){
			$this->db->join('provincias p', 'l.id_privincia = p.id');
			$this->db->where('(l.id_privincia = "'.$provincia.'" OR p.provincia = "'.$provincia.'")');
		}
		$this->db->order_by('l.localidad', 'asc');
		$q = $this->db->get('localidades l');
		foreach ($q->result() as $key => $value) {
			$res[] = $value->localidad;
		}
		return $q->result_array();
		//return $this->db->last_query();
	}	
	
	//Array 2 Obj
	public function a2o($a) {
		return json_decode( json_encode($a), FALSE);
	}

	public function log($head, $msg) {
		$time = date('Y-m-d H:i:s');
		error_log($time.': '.$head.' | '.$msg."\r\n", 3, 'log/log.log');
	}

	public function arrToAssoc($arr, $key, $value) {
		$array = array();
		if(is_array($arr)) {
			if(isset($arr[0])) {
				for($i = 0; $i < count($arr); $i++) {
					$array[$arr[$i][$key]] = $arr[$i][$value];
				}
			} else {
				$array = $arr;
			}
		}

		return $array;
	}
	

//close class
}
