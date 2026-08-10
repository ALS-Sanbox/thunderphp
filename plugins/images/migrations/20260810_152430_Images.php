<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Images class
 */
class Images extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('filename varchar(255) not null');
        $this->addColumn('path varchar(255) not null');
        $this->addColumn('original_name varchar(255) default null');
        $this->addColumn('size int unsigned not null default 0');
        $this->addColumn('user_id int unsigned default null');
        $this->addColumn('date_created datetime default null');

        $this->addPrimaryKey('id');
        $this->addKey('filename');
        $this->addKey('date_created');

        $this->createTable('images');
    }

    public function down() {
        $this->dropTable('images');
    }
}
