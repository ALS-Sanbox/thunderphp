<?php

namespace UserRoles;

use \Model\Model; 

defined('ROOT') or die("Direct script access denied");

/**
 * Role_permission class
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
}