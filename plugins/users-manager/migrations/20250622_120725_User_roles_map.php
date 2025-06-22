<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * User_roles_map class
 */
class User_roles_map extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('role_id tinyint(1) unsigned default 0');
        $this->addColumn('user_id tinyint(1) unsigned default 0');
        $this->addColumn('disabled tinyint(1) unsigned default 0');

        $this->addPrimaryKey('id');
        $this->addKey('role_id');
        $this->addKey('user_id');
        $this->addKey('disabled');

        $this->createTable('user_roles_map');
    }

    public function down() {
        $this->dropTable('user_roles_map');
    }
}
