<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Pages class
 */
class Pages extends Migration {
    public function up() {
        $this->addColumn('id int(10) unsigned NOT NULL AUTO_INCREMENT');
        $this->addColumn('user_id int(10) unsigned NOT NULL');
        $this->addColumn('title varchar(100) NOT NULL');
        $this->addColumn('display_title tinyint(1) unsigned NOT NULL DEFAULT 1');
        $this->addColumn('description text DEFAULT NULL');
        $this->addColumn('keywords varchar(255) DEFAULT NULL');
        $this->addColumn('slug varchar(255) NOT NULL');
        $this->addColumn('content mediumtext DEFAULT NULL');
        $this->addColumn('advancedcontent mediumtext DEFAULT NULL');
        $this->addColumn('categories varchar(255) DEFAULT NULL');
        $this->addColumn('views int(10) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('disabled tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('advanced tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('date_updated datetime DEFAULT NULL');
        $this->addColumn('date_created datetime DEFAULT NULL');
        $this->addColumn('date_deleted datetime DEFAULT NULL');

        $this->addPrimaryKey('id');
        $this->addKey('user_id');
        $this->addKey('title');
        $this->addKey('slug');
        $this->addKey('views');
        $this->addKey('date_created');
        $this->addKey('date_deleted');

        $this->createTable('pages', [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_uca1400_ai_ci'
        ]);
    }

    public function down() {
        $this->dropTable('pages');
    }
}
