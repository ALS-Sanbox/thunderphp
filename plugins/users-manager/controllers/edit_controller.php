<?php

if(user_can('edit_user')) {

    if (!empty($row)) {
        $postdata = $req->post();
        $filedata = $req->files();
        $postdata['id'] = $row->id;
        $files_ok = true;
        $new_image = null;

        if (!empty($filedata['image']) && $filedata['image']['error'] != UPLOAD_ERR_NO_FILE) {
            $new_image = $req->upload_files('image');

            if (!empty($req->upload_errors)) {
                $files_ok = false;
            }
        }

        unset($postdata['image'], $postdata['currentImage']);

        if ($csrf = csrf_verify($req->post('_token'))) {

            if (user_can('edit_user') && $files_ok) {
                if (isset($postdata['password']) && empty($postdata['password'])) {
                    unset($postdata['password']);
                } else if (!empty($postdata['password'])) {
                    $postdata['password'] = password_hash($postdata['password'], PASSWORD_DEFAULT);
                }

                if ($new_image) {
                    $postdata['image'] = $new_image;
                }

                $postdata['date_updated'] = date('Y-m-d H:i:s');
                unset($postdata['id']);

                $user->update($row->id, $postdata);

                if ($new_image && !empty($row->image)) {
                    $req->delete_file($row->image);
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
                            $user_roles_map->update($result->id, ['disabled' => 0]);
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
            } elseif (!$files_ok) {
                message(implode(' ', $req->upload_errors), 'fail');
            }
        } else {
            $user->errors['email'] = "Form Expired!";
        }

        set_value('errors', $user->errors);
    } else {
        message("Record not found", "fail");
    }
}
