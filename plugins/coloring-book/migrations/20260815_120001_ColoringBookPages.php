<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * ColoringBookPages class
 *
 * No SQL FOREIGN KEY constraint on coloring_book_id - matching this
 * codebase's existing convention (no migration anywhere declares one;
 * relationships are managed at the application level, e.g. redirects/
 * not_found_log). Cascading delete (removing a book's pages, and their
 * SVG/thumbnail files, when the book itself is deleted) is handled in
 * ColoringBookController's delete flow instead.
 */
class ColoringBookPages extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('coloring_book_id int unsigned not null');
        $this->addColumn('title varchar(255) not null');
        $this->addColumn('slug varchar(255) not null');
        $this->addColumn('svg_path varchar(500) default null');
        $this->addColumn('thumbnail_path varchar(500) default null');
        $this->addColumn('sort_order int not null default 0');
        $this->addColumn("status enum('draft','published') not null default 'draft'");
        $this->addColumn('date_created datetime not null');
        $this->addColumn('date_updated datetime default null');

        $this->addPrimaryKey('id');
        $this->addKey('coloring_book_id');
        $this->addKey('status');
        $this->addKey('sort_order');

        $this->createTable('coloring_book_pages');
    }

    public function down() {
        $this->dropTable('coloring_book_pages');
    }
}
