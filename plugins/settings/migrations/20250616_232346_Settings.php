<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Settings class
 */
class Settings extends Migration {
    public function up() {
        $this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
        $this->addColumn('`key` varchar(255) NOT NULL');
        $this->addColumn('`value` text NOT NULL');
        $this->addColumn("`type` enum('string','int','bool','json','float') NOT NULL DEFAULT 'string'");
        $this->addColumn("`environment` varchar(50) NOT NULL DEFAULT 'production'");
        $this->addColumn("`updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()");

        $this->addPrimaryKey('id');
        $this->addKey('key');
        $this->addKey('type');
        $this->addKey('environment');

        $this->createTable('settings');
    }

    public function down() {
        $this->dropTable('settings');
    }
}
