<?php
if(user_can('add_user')){

$postdata = $req->post();
$filedata = $req->files();
$files_ok = true;

if(!empty($filedata['image']) && $filedata['image']['error'] != UPLOAD_ERR_NO_FILE)
{
    $userIMG = $req->upload_files('image');

    if (!is_array($userIMG)) {
        $postdata['image'] = $userIMG;
    }else{
        $postdata['image'] = '';
    }

	if(!empty($req->upload_errors))
		$files_ok = false;
}

if(csrf_verify($req->post('_token')) && $files_ok && $user->validate_insert($postdata)){
    $postdata['password'] = password_hash($postdata['password'], PASSWORD_DEFAULT);
    $postdata['date_created'] = date("Y-m-d H:i:s");

    $roledata = [];
    foreach ($postdata as $key => $role_id) {
        if (strstr($key, "role_")) {
            $roledata[] = $role_id;
        }
    }

    $user->insert($postdata);
    $new_user_id = $user->insert_id;

    if (user_can('edit_role')) {
        foreach ($roledata as $role_id) {
            $user_roles_map->create([
                'role_id' => $role_id,
                'user_id' => $new_user_id,
                'disabled' => 0,
            ]);
        }
    }

    message("Page added successfully!", "success");
    redirect($admin_route . '/' . $plugin_route . '/view/' . $new_user_id);
} elseif (!$files_ok) {
    message(implode(' ', $req->upload_errors), 'fail');
} else {
    message(implode(' ', $user->errors),'fail');
}

}