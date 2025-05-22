<?php
if(user_can('add_user')){

$postdata = $req->post();
$filedata = $req->files();
$files_ok = true;

if(!empty($filedata))
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

    $user->insert($postdata);

    message("Page added successfully!", "success");
    redirect($admin_route . '/' . $plugin_route . '/view/' . $user->insert_id);
}

message(implode(' ', $user->errors),'fail');

}