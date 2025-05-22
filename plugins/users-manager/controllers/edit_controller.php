<?php

if(user_can('edit_user')) {

    if (!empty($row)) {
        $postdata = $req->post();
        $filedata = $req->files();
        $postdata['id'] = $row->id;
        $files_ok = true;

        if (!empty($filedata['image']) && $filedata['image']['error'] != UPLOAD_ERR_NO_FILE) {

            $req->delete_file($row->image);

            $postdata['image'] = $req->upload_files('image');

            if (!empty($req->upload_errors)) {
                $files_ok = false;
            }
        } else {
            $postdata['image'] = $_POST['currentImage'];
        }

        if ($csrf = csrf_verify($req->post('_token'))) {
            
            if (user_can('edit_user')) {
                if (isset($postdata['password']) && empty($postdata['password'])) {
                    unset($postdata['password']);
                } else if (!empty($postdata['password'])) {
                    $postdata['password'] = password_hash($postdata['password'], PASSWORD_DEFAULT);
                }

                $postdata['date_updated'] = date('Y-m-d H:i:s');
                unset($postdata['id']);
                
                $user->update($row->id, $postdata);

                if (!empty($postdata['image']) && file_exists($row->image)) {
                    unlink($row->image);
                }

                $user_id = $row->id;
                if (user_can('edit_role')) {
                    $roledata = [];
                    foreach ($postdata as $key => $role_id) {
                        if (strstr($key, "role_")) {
                            $roledata[] = $role_id;
                        }
                    }

                    $user_roles_map->query('UPDATE ' . $vars['optional_tables']['roles_map_table'] . ' SET disabled = 1 WHERE user_id = :user_id', ['user_id' => $user_id]);

                    foreach ($roledata as $role_id) {
                        $result = $user_roles_map->first(['role_id' => $role_id, 'user_id' => $user_id]);
                        if ($result) {
                            $user_roles_map->update($result->role_id, ['disabled' => 0]);
                        } else {
                            $user_roles_map->insert([
                                'role_id' => $role_id,
                                'user_id' => $user_id,
                                'disabled' => 0
                            ]);
                        }
                    }
                }

                message("Record edited successfully!", "success");
                redirect($admin_route . '/' . $plugin_route . '/view/' . $row->id);
            }
        } else {
            $user->errors['email'] = "Form Expired!";
        }

        set_value('errors', $user->errors);
    } else {
        message("Record not found", "fail");
    }
}
