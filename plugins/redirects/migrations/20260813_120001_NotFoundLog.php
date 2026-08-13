<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * NotFoundLog class
 */
class NotFoundLog extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('url varchar(255) not null');
        $this->addColumn('hits int unsigned not null default 1');
        $this->addColumn('first_seen datetime not null');
        $this->addColumn('last_seen datetime not null');

        $this->addPrimaryKey('id');
        $this->addUniqueKey('url');

        $this->createTable('not_found_log');
    }

    public function down() {
        $this->dropTable('not_found_log');
    }
}
