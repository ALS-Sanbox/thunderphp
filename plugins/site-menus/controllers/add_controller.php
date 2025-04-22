<?php

if(user_can('add_menu')){

$postdata = $req->post();
$filedata = $req->files();
$files_ok = true;
if (!empty($filedata)) {

    $icon = $req->upload_files('icon');
    if (!is_array($icon)) {
        $postdata['image'] = $icon;
    }else{
        $postdata['image'] = '';
    }

    $mega_image = $req->upload_files('mega_image');
    if (!is_array($mega_image)) {
        $postdata['mega_image'] = $mega_image;
    }else{
        $postdata['mega_image'] = '';
    }

    if (!empty($req->upload_errors)) {
        $files_ok = false;
    }
}


if(csrf_verify($req->post('_token')) && $menus->validate_insert($postdata)){
    if (!isset($postdata['is_mega'])) {
        $postdata['is_mega'] = 0;
    }else{
        $postdata['is_mega'] = 1;
    }

    if (!isset($postdata['active'])) {
        $postdata['disabled'] = 1;
    }else{
        $postdata['disabled'] = 0;
    }

    $menus->insert($postdata);

    message("Record added successfully!", "success");
    redirect($admin_route . '/' . $plugin_route . '/view/' . $menus->insert_id);
}

message(implode(' ', $menus->errors),'fail');

}