<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * ColoringBooks class
 */
class ColoringBooks extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('title varchar(255) not null');
        $this->addColumn('slug varchar(255) not null unique');
        $this->addColumn('description text default null');
        $this->addColumn('cover_image varchar(500) default null');
        $this->addColumn("status enum('draft','published') not null default 'draft'");
        $this->addColumn('sort_order int not null default 0');
        $this->addColumn('date_created datetime not null');
        $this->addColumn('date_updated datetime default null');

        $this->addPrimaryKey('id');
        $this->addKey('status');
        $this->addKey('sort_order');

        $this->createTable('coloring_books');
    }

    public function down() {
        $this->dropTable('coloring_books');
    }
}
