<?php

namespace Roles;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

/**
 * Shared across the users-manager and user-roles plugins - see the note in
 * User_role.php for why this lives in app/models/ instead of either plugin.
 */
class User_roles_map extends Model
{
	protected $table = 'user_roles_map';
	public $primary_key = 'id';

	protected $allowedColumns = [
		'role_id',
		'user_id',
		'disabled',
	];

	protected $allowedUpdateColumns = [
		'role_id',
		'user_id',
		'disabled',
	];
}
