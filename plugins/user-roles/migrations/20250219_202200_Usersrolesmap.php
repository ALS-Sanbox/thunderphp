<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Usersrolesmap class
 */
class Usersrolesmap extends Migration {
    public function up()
	{
		$this->addColumn('id int unsigned auto_increment');
		$this->addColumn('role_id int default 0');
		$this->addColumn('user_id int default 0');
		$this->addColumn('disabled tinyint(1) unsigned default 0');

		$this->addPrimaryKey('id');
		$this->addKey('disabled');

		$this->createTable('user_roles_map');

		$this->addData([
		 	'role_id'	=> 1,
		 	'user_id'	=> 1,
		 	'disabled'	=> 0,
		]);

		$this->insert('user_roles_map');
	}

	public function down()
	{
		$this->dropTable('user_roles_map');
	}
}