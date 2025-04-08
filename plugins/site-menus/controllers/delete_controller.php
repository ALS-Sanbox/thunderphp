<?php

if(user_can('delete_menu')){
    $menu_id = $req->post('menu_id');
    
    if (csrf_verify($req->post('_token')) && !empty($menu_id)) {
        $menu = $menus->find($menu_id);
        if ($menu) {
            $image_path = $menu->image;
            $mega_image_path = $menu->mega_image;

            if (!empty($image_path) && file_exists($image_path)) {
                unlink($image_path);
            }

            if (!empty($mega_image_path) && file_exists($mega_image_path)) {
                unlink($mega_image_path);
            }
        }

        if ($menus->delete($menu_id)) {
            message("Record and images deleted successfully!", "success");
            redirect($admin_route . '/' . $plugin_route . '/list');
        } else {
            message("Failed to delete record.", "fail");
        }
    } else {
        message("Invalid request.", "fail");
    }
}
?>
