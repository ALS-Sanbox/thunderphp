<?php

namespace Hestia\WebApp\Installers\ThunderPHP;

use Hestia\WebApp\Installers\BaseSetup as BaseSetup;

class ThunderPHPSetup extends BaseSetup {
	protected $appname = "thunderphp";

	protected $appInfo = [
		"name" => "ThunderPHP",
		"group" => "framework",
		"enabled" => true,
		"version" => "latest",
		"thumbnail" => "thunderphp-thumb.png",
	];

	protected $config = [
		"form" => [
			"site_name" => ["type" => "text", "value" => "ThunderPHP"],
			"admin_email" => "text",
			"admin_first_name" => "text",
			"admin_last_name" => "text",
			"admin_password" => "password",
		],
		"database" => true,
		"resources" => [
			"archive" => [
				"src" => "https://github.com/ALS-Sanbox/thunderphp/archive/refs/heads/main.tar.gz",
			],
		],
		"server" => [
			"nginx" => [
				"template" => "default",
			],
			"php" => [
				"supported" => ["8.0", "8.1", "8.2", "8.3", "8.4"],
			],
		],
	];

	public function install(array $options = null): bool {
		parent::install($options);
		parent::setup($options);

		// database_name/database_user come back from the install form as the
		// raw names the admin typed - HestiaCP's own v-add-database already
		// provisioned the real database under the {hestia_user}_{name}
		// convention, so the actual name has to be reconstructed here too
		// (same pattern DrupalSetup.php uses for its own --db-url).
		$dbName = $this->appcontext->user() . "_" . $options["database_name"];
		$dbUser = $this->appcontext->user() . "_" . $options["database_user"];

		$this->appcontext->runUser(
			"v-run-cli-cmd",
			[
				"/usr/bin/php" . $options["php_version"],
				$this->getDocRoot("thunder"),
				"do:install",
				"--db-host=" . $options["database_host"],
				"--db-name=" . $dbName,
				"--db-user=" . $dbUser,
				"--db-password=" . $options["database_password"],
				"--site-url=https://" . $this->domain,
				"--site-name=" . $options["site_name"],
				"--admin-email=" . $options["admin_email"],
				"--admin-password=" . $options["admin_password"],
				"--admin-first-name=" . $options["admin_first_name"],
				"--admin-last-name=" . $options["admin_last_name"],
			],
			$status,
		);

		return $status->code === 0;
	}
}
