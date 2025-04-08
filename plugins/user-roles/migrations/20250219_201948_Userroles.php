<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Userroles class
 */
class Userroles extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('role varchar(100) not null unique');
        $this->addColumn('disabled tinyint(1) unsigned default 0');

        $this->addPrimaryKey('id');
        $this->addKey('disabled');

        $this->addData([
            'role' => 'admin',
            'disabled' => 0,
       ]);
       
        $this->createTable('user_roles');         
    }

    public function down() {
        $this->dropTable('user_roles');
    }
}