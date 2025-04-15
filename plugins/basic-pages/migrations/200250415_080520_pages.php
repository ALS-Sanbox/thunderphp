<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Pages class
 */
class Pages extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('title varchar(100) not null');
        $this->addColumn('display_title tinyint(1) unsigned default 1');
        $this->addColumn('description text null');
        $this->addColumn('keywords varchar(255) null');
        $this->addColumn('slug varchar(255) not null');
        $this->addColumn('content mediumtext null');
        $this->addColumn('views int unsigned default 0');
        $this->addColumn('image varchar(1024) null');
        $this->addColumn('date datetime null');
        $this->addColumn('disabled tinyint(1) unsigned default 0');
        $this->addColumn('date_updated datetime null');
        $this->addColumn('date_created datetime null');
        $this->addColumn('date_deleted datetime null');

        $this->addPrimaryKey('id');
        $this->addKey('user_id');
        $this->addKey('title');
        $this->addKey('slug');
        $this->addKey('views');
        $this->addKey('date_created');
        $this->addKey('date_deleted');

        $this->createTable('pages');
    }

    public function down() {
        $this->dropTable('pages');
    }
}