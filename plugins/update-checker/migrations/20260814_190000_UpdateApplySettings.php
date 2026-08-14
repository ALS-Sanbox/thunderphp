<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * UpdateApplySettings class
 *
 * Additional settings for the download-and-apply feature, seeded the same
 * way as UpdateCheckerSettings.
 */
class UpdateApplySettings extends Migration {
    public function up() {
        $defaultSettings = [
            ['key' => 'update_check_latest_zipball_url', 'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'update_check_auto_apply',          'value' => '0', 'type' => 'bool',   'environment' => 'production'],
            ['key' => 'update_check_last_apply_report',   'value' => '', 'type' => 'string', 'environment' => 'production'],
        ];

        foreach ($defaultSettings as $row) {
            $this->addData($row);
        }

        $this->insert('settings');
    }

    public function down() {
        $this->query("DELETE FROM settings WHERE `key` IN ('update_check_latest_zipball_url','update_check_auto_apply','update_check_last_apply_report')");
    }
}
