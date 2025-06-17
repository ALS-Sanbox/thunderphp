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
		
		// Insert sample settings data
        $this->db->query("
            INSERT INTO settings (`id`, `key`, `value`, `type`, `environment`, `updated_at`) VALUES
            (1, 'site_name', 'Thunder PHP', 'string', 'production', '2025-06-17 07:04:13'),
            (2, 'site_description', 'A clone of wordpress', 'string', 'production', '2025-06-17 07:04:13'),
            (3, 'debug_mode', '1', 'bool', 'production', '2025-06-17 07:04:13'),
            (4, 'max_upload_size', '10', 'int', 'production', '2025-06-17 07:04:13'),
            (5, 'pagination_limit', '25', 'int', 'production', '2025-06-17 07:04:13'),
            (6, 'site_homepage', 'home', 'string', 'production', '2025-06-17 07:04:13'),
            (7, 'site_url', 'Https://your-site.com', 'string', 'production', '2025-06-17 07:04:13'),
            (8, 'admin_email', 'admin@your-site.com', 'string', 'production', '2025-06-17 07:04:13')
        ");
    }

    public function down() {
        $this->dropTable('settings');
    }
}
