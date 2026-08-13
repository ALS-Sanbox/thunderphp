<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * LoginAttempts class
 */
class LoginAttempts extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('ip_address varchar(45) not null');
        $this->addColumn('email varchar(255) not null');
        $this->addColumn('attempts int unsigned not null default 1');
        $this->addColumn('first_attempt_at datetime not null');

        $this->addPrimaryKey('id');
        $this->addKey('ip_address');
        $this->addKey('email');

        $this->createTable('login_attempts');
    }

    public function down() {
        $this->dropTable('login_attempts');
    }
}
