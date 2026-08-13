<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * ActivityLog class
 */
class ActivityLog extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned default null');
        $this->addColumn('username varchar(255) default null');
        $this->addColumn('action varchar(20) not null');
        $this->addColumn('entity_type varchar(100) not null');
        $this->addColumn('ip_address varchar(45) default null');
        $this->addColumn('date_created datetime not null');

        $this->addPrimaryKey('id');
        $this->addKey('entity_type');
        $this->addKey('user_id');
        $this->addKey('date_created');

        $this->createTable('activity_log');
    }

    public function down() {
        $this->dropTable('activity_log');
    }
}
