<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * User_roles class
 */
class User_roles extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('role varchar(255) not null unique');
        $this->addColumn('disabled tinyint(1) unsigned default 0');

        $this->addPrimaryKey('id');
        $this->addKey('role');
        $this->addKey('disabled');

        $this->createTable('user_roles');         
    }

    public function down() {
        $this->dropTable('user_roles');
    }
}
