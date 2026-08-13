<?php

namespace Roles;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

/**
 * Shared across the users-manager and user-roles plugins. Framework-level
 * (not plugin-owned) because this app's autoloader resolves an unmatched
 * class from the *calling* plugin's own models/ folder, not the class's
 * namespace - a plugin-owned model can never be new'd from another plugin.
 */
class User_role extends Model
{
	protected $table = 'user_roles';
	public $primary_key = 'id';
	protected $allowedColumns = [
		'role',
		'disabled',
	];

	protected $allowedUpdateColumns = [
		'role',
		'disabled',
	];

	public function validate_insert(array $data):bool
	{
 		if(empty($data['role']))
 		{
 			$this->errors['role'] = 'Role is required';
 		}else
 		if($this->first(['role'=>$data['role']]))
 		{
 			$this->errors['role'] = 'That role is already in use';
 		}else
 		if(!preg_match("/^[a-zA-Z ]+$/", $data['role']))
 		{
 			$this->errors['role'] = 'Invalid: Role cannot have numbers.';
 		}
 		return empty($this->errors);
	}

	public function validate_update(array $data):bool
	{
		$role_arr = [
			'role'=>$data['role']
		];
		$role_arr_not = [
			$this->primary_key => $data[$this->primary_key] ?? 0
		];

		if(empty($data['role']))
 		{
 			$this->errors['role'] = 'Role is required';
 		}else
 		if($this->first($role_arr, $role_arr_not))
 		{
 			$this->errors['role'] = 'That Role is already in use';
 		}else
 		if(!preg_match("/^[a-zA-Z ]+$/", $data['role']))
 		{
 		 	$this->errors['role'] = 'Invalid: Role cannot have numbers.';
 		}
		return empty($this->errors);
	}

	public function insert(array $data): bool {
        return $this->create($data);
    }

	public function update_role(int $id, array $data): mixed {
        return $this->update($id, $data);
    }
}
