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
			// No site_name field here on purpose - see install()'s comment
			// below. do:install defaults it to "ThunderPHP"; trivial to
			// rename afterward from the dashboard's Settings screen.
			"admin_email" => "text",
			"admin_first_name" => "text",
			"admin_last_name" => "text",
			"admin_password" => "password",
		],
		"database" => true,
		"resources" => [
			"archive" => [
				// do:install (what this class runs below) only exists on
				// `nightly` right now - `main` doesn't have it yet. Point
				// this at `main` once that work is promoted to the stable
				// branch; nightly's tarball is the only one this actually
				// works against today.
				"src" => "https://github.com/ALS-Sanbox/thunderphp/archive/refs/heads/nightly.tar.gz",
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
		// v-run-cli-cmd rebuilds every argument into one unquoted string
		// before exec'ing it (see its own source - it never keeps each
		// argument as a separate shell-escaped token past that point), so
		// any value containing a space gets silently word-split there and
		// truncated at the first space, no error raised. Confirmed live:
		// a site_name of "My Test Site" arrived as just "My" - the rest
		// wasn't merely dropped, it became stray unflagged tokens that
		// do:install's own parser ignores. That's a cosmetic problem for
		// site_name (not collected here at all for exactly this reason -
		// do:install just defaults it), but a real lockout risk for the
		// admin password: depending on what's chosen, the truncated
		// version can still pass do:install's own length/complexity check
		// and get silently stored instead of what was actually typed, with
		// no error shown anywhere. Reject it here instead - before the
		// archive download/extraction and database creation below, not
		// just before the CLI call, so a doomed install fails fast.
		if (str_contains($options["admin_password"], " ")) {
			throw new \Exception(
				"ThunderPHP admin password can't contain spaces (a HestiaCP CLI limitation, not a ThunderPHP one - see ThunderPHPSetup::install()).",
			);
		}

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
