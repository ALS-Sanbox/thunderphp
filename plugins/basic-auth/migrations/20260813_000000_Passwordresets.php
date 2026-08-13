<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Passwordresets class
 */
class Passwordresets extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('token_hash char(64) not null');
        $this->addColumn('expires_at datetime not null');
        $this->addColumn('used_at datetime default null');
        $this->addColumn('created_at timestamp not null default current_timestamp()');

        $this->addPrimaryKey('id');
        $this->addUniqueKey('token_hash');
        $this->addKey('user_id');

        $this->createTable('password_resets');
    }

    public function down() {
        $this->dropTable('password_resets');
    }
}
