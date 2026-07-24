<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_tournament_type_to_reservations extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_column('reservations', array(
			'tournament_type' => array(
				'type' => 'ENUM',
				'constraint' => array('singles', 'doubles'),
				'default' => 'singles',
				'null' => FALSE,
				'after' => 'category'
			)
		));
	}

	public function down()
	{
		$this->dbforge->drop_column('reservations', 'tournament_type');
	}

}
