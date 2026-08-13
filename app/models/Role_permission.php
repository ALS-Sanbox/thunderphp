<?php

namespace Roles;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

/**
 * Shared across the users-manager and user-roles plugins - see the note in
 * User_role.php for why this lives in app/models/ instead of either plugin.
 */
class Role_permission extends Model
{
	protected $table = 'permission_roles';
	public $primary_key = 'id';

	protected $allowedColumns = [
		'role_id',
		'permission',
		'disabled',
	];

	protected $allowedUpdateColumns = [
		'role_id',
		'permission',
		'disabled',
	];

	public function insert(array $data): bool {
        return $this->create($data);
    }
}
