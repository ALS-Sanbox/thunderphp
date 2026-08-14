<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * GoogleAccounts class
 *
 * A separate table (rather than a `siteusers.google_id` column) so linking
 * a Google identity to an account never requires an ALTER TABLE on
 * `siteusers` - a table basic-auth and users-manager both already depend on.
 */
class GoogleAccounts extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('google_sub varchar(255) not null');
        $this->addColumn('google_email varchar(255) not null');
        $this->addColumn('date_created datetime not null');

        $this->addPrimaryKey('id');
        $this->addUniqueKey('google_sub');
        $this->addKey('user_id');

        $this->createTable('google_accounts');
    }

    public function down() {
        $this->dropTable('google_accounts');
    }
}
