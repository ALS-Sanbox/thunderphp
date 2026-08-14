<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * UpdateCheckerSettings class
 *
 * Seeds update-checker keys into the shared `settings` table, same pattern
 * as SmtpSettings/SeoSettings/GoogleAuthSettings.
 */
class UpdateCheckerSettings extends Migration {
    public function up() {
        $defaultSettings = [
            ['key' => 'update_check_enabled',          'value' => '1', 'type' => 'bool',   'environment' => 'production'],
            ['key' => 'update_check_last_checked_at',   'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'update_check_latest_version',    'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'update_check_latest_url',        'value' => '', 'type' => 'string', 'environment' => 'production'],
        ];

        foreach ($defaultSettings as $row) {
            $this->addData($row);
        }

        $this->insert('settings');
    }

    public function down() {
        $this->query("DELETE FROM settings WHERE `key` IN ('update_check_enabled','update_check_last_checked_at','update_check_latest_version','update_check_latest_url')");
    }
}
