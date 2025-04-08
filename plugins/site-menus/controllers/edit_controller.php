<?php

if(user_can('edit_menu')){

    $postdata = $req->post();
    $filedata = $req->files();
    $files_ok = true;

    $menu_id = $row->id;
    $existing_menu = $menus->find($menu_id);

    if (!$existing_menu) {
        message("Menu not found.", "fail");
        redirect($admin_route . '/' . $plugin_route . '/view');
    }

    $uploaded_icon = null;
    $uploaded_mega_image = null;

    if (!empty($filedata)) {

        $icon = $req->upload_files('icon');
        if (!is_array($icon)) {
            $postdata['image'] = $icon;
            $uploaded_icon = $icon;
        } else {
            $postdata['image'] = $existing_menu->image;
        }

        $mega_image = $req->upload_files('mega_image');
        if (!is_array($mega_image)) {
            $postdata['mega_image'] = $mega_image;
            $uploaded_mega_image = $mega_image;
        } else {
            $postdata['mega_image'] = $existing_menu->mega_image;
        }

        if (!empty($req->upload_errors)) {
            $files_ok = false;
        }
    }

    if (!$files_ok) {
        if ($uploaded_icon && file_exists($uploaded_icon)) {
            unlink($uploaded_icon);
        }
        if ($uploaded_mega_image && file_exists($uploaded_mega_image)) {
            unlink($uploaded_mega_image);
        }

        message("File upload failed. Please try again.", "fail");
        //redirect($admin_route . '/' . $plugin_route . '/view');
    }

    if(csrf_verify($req->post('_token'))){

        if (isset($postdata['title']) && $postdata['title'] == $existing_menu->title) {
            unset($postdata['title']);
        }
        if (isset($postdata['slug']) && $postdata['slug'] == $existing_menu->slug) {
            unset($postdata['slug']);
        }
        if (isset($postdata['parent']) && $postdata['parent'] == $existing_menu->parent) {
            unset($postdata['parent']);
        }
        if (isset($postdata['permission']) && $postdata['permission'] == $existing_menu->permission) {
            unset($postdata['permission']);
        }
        if (!isset($postdata['is_mega']) || $postdata['is_mega'] == $existing_menu->is_mega) {
            unset($postdata['is_mega']);
        } else {
            $postdata['is_mega'] = isset($postdata['is_mega']) ? 1 : 0;
        }

        if (!isset($postdata['disabled']) || $postdata['disabled'] == $existing_menu->disabled) {
            unset($postdata['disabled']);
        } else {
            $postdata['disabled'] = isset($postdata['active']) ? 0 : 1;
        }

        if ($menus->update($menu_id, $postdata)) {
            message("Record updated successfully!", "success");
            redirect($admin_route . '/' . $plugin_route . '/view/' . $menu_id);
        } else {
            message("Failed to update the menu.", "fail");

            if ($uploaded_icon && file_exists($uploaded_icon)) {
                 $req->delete_file($uploaded_icon);
            }
            if ($uploaded_mega_image && file_exists($uploaded_mega_image)) {
                $req->delete_file($uploaded_mega_image);
            }

            //redirect($admin_route . '/' . $plugin_route . '/view');
        }
    }

    message(implode(' ', $menus->errors), 'fail');
}

?>
