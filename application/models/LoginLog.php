<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginLog extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function logLogin($user_id, $dni, $name) {
		$ip = $this->input->ip_address();
		$data = array(
			'user_id' => $user_id,
			'dni' => $dni,
			'name' => $name,
			'login_time' => date('Y-m-d H:i:s'),
			'ip_address' => $ip
		);
		return $this->db->insert('login_logs', $data);
	}

	public function logLogout($user_id) {
		$data = array('logout_time' => date('Y-m-d H:i:s'));
		return $this->db->where('user_id', $user_id)
			->where('logout_time IS NULL')
			->update('login_logs', $data);
	}

	public function getLogs($limit = 100, $offset = 0) {
		$sql = "SELECT ll.*,
				CASE WHEN EXISTS(
					SELECT 1 FROM reservations_partners rp
					INNER JOIN reservations r ON r.id = rp.reservation_id
					WHERE rp.partner_id = ll.user_id AND r.tournament_type = 'doubles'
				) THEN 'YES' ELSE 'NO' END as is_inscripto_dobles
				FROM login_logs ll
				ORDER BY ll.login_time DESC
				LIMIT " . intval($offset) . ", " . intval($limit);
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function getLogsByUser($user_id, $limit = 50) {
		$query = $this->db->where('user_id', $user_id)
			->order_by('login_time', 'DESC')
			->limit($limit)
			->get('login_logs');
		return $query->result();
	}

	public function getTotalLogs() {
		return $this->db->count_all('login_logs');
	}

	public function getLogsByDateRange($start_date, $end_date) {
		$query = $this->db->where('login_time >=', $start_date)
			->where('login_time <=', $end_date)
			->order_by('login_time', 'DESC')
			->get('login_logs');
		return $query->result();
	}

	public function getLogsByDni($dni) {
		$query = $this->db->where('dni', $dni)
			->order_by('login_time', 'DESC')
			->get('login_logs');
		return $query->result();
	}
}
?>
