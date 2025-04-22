<?php
//list_controller.php
message('');
if (!isset($user_roles) || !isset($permissions)) {
    die("Error: Required objects not initialized!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $postdata = $req->post();
    $action = $_POST['action'];
    $roleId = $postdata['id'] = $_POST['role_id'] ?? null;
    $newName = $_POST['rename_role'] ?? null;
    $role = $_POST['role'] ?? null;
    $changedPermissions = $_POST['permissions'] ?? [];
    $activeStatus = $postdata['disabled'] = $_POST['active'] ?? 1;
    $addRole = $_POST['add_role'] ?? null;
    $csrf = csrf_verify($_POST['_token']);

    switch ($action) {
        case 'update':
            if (!empty($roleId)) {             
                if($csrf && $user_roles->validate_update($postdata))
                {
                    if(user_can('edit_role'))
                    {
                        $permissions->query("UPDATE permission_roles SET disabled = 1 WHERE role_id = ?", [$roleId]);

                        foreach ($changedPermissions as $perm) {
                            $row = $permissions->first(['role_id'=>$roleId,'permission'=>$perm]);

                            if ($row) {
                                $permissions->update($row->id,['disabled'=>0]);
                            } else {
                                $permissions->insert([
                                    'role_id' => $roleId,
                                    'permission' => $perm,
                                    'disabled' => 0,
                                ]);
                            }
                        }

                        $user_roles->update($roleId, $postdata);
                        message($role. ' has been updated.', 'success');
                    }

                }else{
                    set_value('errors', $user_roles->errors);
                }

                if(!$csrf)
                    $user_roles->errors['role'] = "Form Expired!";

            } else {
                echo "Role name cannot be empty.";
            }
            break;

        case 'rename':
            if (!empty($newName)) {
                $postdata['role'] = $newName;

                if($csrf && $user_roles->validate_update($postdata))
                {
                    if(user_can('edit_role'))
                    {
                        unset($postdata['add_role']);
                        unset($postdata['active']);
                        unset($postdata['permissions']);
                        unset($postdata['id']);
                        $user_roles->update($roleId, $postdata);
                        message($role. ' has been changed to '. $newName, 'success');
                    }

                }else{
                    set_value('errors', $user_roles->errors);
                }

                if(!$csrf)
                    $user_roles->errors['role'] = "Form Expired!";

            } else {
                echo "Role name cannot be empty.";
            }
            break;

        case 'delete':
         
            if($csrf)
            {
                if(user_can('delete_role'))
                {
                    $user_roles->delete($roleId);
					
					$perm = $permissions->query("SELECT * FROM permission_roles WHERE role_id = ?", [$roleId]);
					foreach ($perm as $entry) {
						$permissions->delete($entry->id);
					}
					
					$map = $user_map->query("SELECT * FROM user_roles_map WHERE role_id = ?", [$roleId]);
					foreach ($map as $entry) {
						$user_map->delete($entry->id);
					}
					
                    message('Role deleted successfully!','success');
                }else{
                    set_value('errors', $user_roles->errors);
                    message(get_value('errors')['role']);

                }
            }

            if(!$csrf)
                    $user_roles->errors['role'] = "Form Expired!";

            break;

        case 'add':
            if (!empty($addRole)) {
                $postdata['role'] = $addRole;

                if($csrf && $user_roles->validate_insert($postdata))
                {
                    if(user_can('add_role'))
                    {
                        $user_roles->insert($postdata);
                        message('Role '.$addRole.' added successfully!','success');
                    }

                }else{

                    set_value('errors', $user_roles->errors);
                    message(get_value('errors')['role']);
                }
                
                if(!$csrf)
                    $user_roles->errors['role'] = "Form Expired!";
                
            } else {
               message("Role name cannot be empty.");
            }
            break;

        default:
            echo("Invalid action.");
            break;
    }
}


      
