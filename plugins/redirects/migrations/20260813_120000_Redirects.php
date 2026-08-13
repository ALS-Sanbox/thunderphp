<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Redirects class
 */
class Redirects extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('from_path varchar(255) not null');
        $this->addColumn('to_path varchar(255) not null');
        $this->addColumn("redirect_type enum('301','302') not null default '301'");
        $this->addColumn('disabled tinyint(1) unsigned not null default 0');
        $this->addColumn('hits int unsigned not null default 0');
        $this->addColumn('date_created datetime not null');

        $this->addPrimaryKey('id');
        $this->addUniqueKey('from_path');
        $this->addKey('disabled');

        $this->createTable('redirects');
    }

    public function down() {
        $this->dropTable('redirects');
    }
}
