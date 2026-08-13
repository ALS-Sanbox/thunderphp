<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * UserProfiles class
 */
class UserProfiles extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('bio text default null');
        $this->addColumn('website varchar(255) default null');
        $this->addColumn('date_updated datetime default null');

        $this->addPrimaryKey('id');
        $this->addUniqueKey('user_id');

        $this->createTable('user_profiles');
    }

    public function down() {
        $this->dropTable('user_profiles');
    }
}
