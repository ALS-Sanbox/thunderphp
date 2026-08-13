<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * SiteLogoSetting class
 */
class SiteLogoSetting extends Migration {
    public function up() {
        $this->addData([
            'key' => 'site_logo', 'value' => '', 'type' => 'string', 'environment' => 'production',
        ]);

        $this->insert('settings');
    }

    public function down() {
        $this->query("DELETE FROM settings WHERE `key` = 'site_logo'");
    }
}
