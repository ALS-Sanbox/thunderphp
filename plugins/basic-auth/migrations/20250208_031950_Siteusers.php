<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Siteusers class
 */
class Siteusers extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('first_name varchar(255) not null');
        $this->addColumn('last_name varchar(255) not null');
        $this->addColumn('email varchar(255) not null unique');
        $this->addColumn('password varchar(255) not null');
        $this->addColumn('image varchar(255) default null'); // Assuming image will store the file path or URL
        $this->addColumn('deleted tinyint(1) unsigned default 0');
        $this->addColumn('date_created datetime default null');
        $this->addColumn('date_updated datetime default null');
        $this->addColumn('date_deleted datetime default null');

        $this->addPrimaryKey('id');
        $this->addKey('first_name');
        $this->addKey('last_name');
        $this->addKey('email');
        $this->addKey('deleted');
        $this->addKey('date_created');
        $this->addKey('date_deleted');

        $this->createTable('siteusers');

        // No seeded admin account here on purpose - the install wizard
        // (install.php) creates the one real admin account with
        // credentials the installer actually chooses, instead of every
        // fresh install shipping the same known email/password.
    }

    public function down() {
        $this->dropTable('siteusers');
    }
}