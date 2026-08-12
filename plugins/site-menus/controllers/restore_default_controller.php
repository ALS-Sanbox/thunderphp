<?php
if (user_can('add_menu')) {
    $existing = $menus->first(['slug' => 'home', 'parent' => 0]);

    if ($existing) {
        message('The default Home menu item already exists.', 'fail');
    } elseif (csrf_verify($req->post('_token'))) {
        $menus->insert([
            'title'      => 'Home',
            'slug'       => 'home',
            'parent'     => 0,
            'is_mega'    => 0,
            'disabled'   => 0,
            'permission' => '',
            'list_order' => 1,
        ]);
        message('Default Home menu item restored!', 'success');
    } else {
        message('Invalid request.', 'fail');
    }

    redirect($admin_route . '/' . $plugin_route);
}
