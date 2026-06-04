<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invitacion extends CI_Controller {

    private $secret = 'baltc_2ndchance_2026';

    public function __construct() {
        parent::__construct();
        $this->load->model('User');
    }

    public function aceptar2ndchance() {
        $this->protect->setRequest('GET');

        $pid   = intval($this->input->get('pid'));
        $cid   = intval($this->input->get('cid'));
        $token = $this->input->get('t', true);

        $expected = md5($pid . ':' . $cid . ':' . $this->secret);
        $ok           = false;
        $ya_inscripto = false;
        $nombre       = '';
        $categoria    = '';

        if($pid && $cid && $token && hash_equals($expected, $token)) {
            $partner = $this->User->getById($pid);
            $cat     = $this->db->where('id', $cid)->get('category')->row();

            if($partner && $cat) {
                $nombre    = $partner->name;
                $categoria = $cat->name;

                // Verificar si ya está inscripto
                $ya = $this->db->select('r.id')->from('reservations r')
                    ->join('reservations_partners rp', 'rp.reservation_id = r.id')
                    ->where('rp.partner_id', $pid)
                    ->where('r.category', $cid)
                    ->get();

                if($ya->num_rows() > 0) {
                    $ya_inscripto = true;
                } else {
                    $this->db->insert('reservations', array('category' => $cid));
                    $res_id = $this->db->insert_id();
                    $this->db->insert('reservations_partners', array(
                        'partner_id'     => $pid,
                        'reservation_id' => $res_id
                    ));
                    $ok = true;
                }
            }
        }

        $d = array(
            'ok'          => $ok,
            'ya_inscripto'=> $ya_inscripto,
            'nombre'      => $nombre,
            'categoria'   => $categoria,
        );
        $this->load->view('web/inscripcion_2ndchance', $d);
    }
}
