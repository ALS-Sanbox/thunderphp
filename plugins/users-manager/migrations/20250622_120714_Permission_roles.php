<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Permission_roles class
 */
class Permission_roles extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('role_id tinyint(1) unsigned default 0');
        $this->addColumn('permission varchar(255) not null unique');
        $this->addColumn('disabled tinyint(1) unsigned default 0');

        $this->addPrimaryKey('id');
        $this->addKey('role_id');
        $this->addKey('permission');

        $this->createTable('permission_roles');

        $this->addData([
        'role_id'       =>'1',
        'permission'    =>'all',
        'disabled'      =>'0',
        ]);
        
         $this->insert('permission_roles');
    }

    public function down() {
        $this->dropTable('permission_roles');
    }
}
