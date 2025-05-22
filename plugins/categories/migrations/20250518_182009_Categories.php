<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Categories class
 */
class Categories extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('category varchar(255) not null unique');
        $this->addColumn('slug varchar(100) not null');
        $this->addColumn('parent_id int unsigned default null');
        $this->addColumn('disabled tinyint(1) unsigned default 0');

        $this->addPrimaryKey('id');
        $this->addKey('category');
        $this->addKey('slug');
        $this->addKey('disabled');
        $this->addKey('parent_id');

        $this->createTable('categories');
    }


    public function down() {
        $this->dropTable('categories');
    }
}