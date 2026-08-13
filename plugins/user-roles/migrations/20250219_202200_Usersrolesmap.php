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

		// No seeded row here on purpose - there's no guaranteed user with
		// id 1 anymore (see the Siteusers migration). The install wizard
		// creates the real admin-role mapping after it creates the admin
		// account, looking up the admin role's actual id instead of
		// assuming it's 1.
	}

	public function down()
	{
		$this->dropTable('user_roles_map');
	}
}
