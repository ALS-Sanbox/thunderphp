<?php
$ids = array_filter(array_map('intval', explode(',', $req->post('selected_ids') ?? '')));
$bulk_action = $req->post('action');

if (!empty($ids) && csrf_verify($req->post('_token') ?? '')) {
    $count = 0;

    if ($bulk_action === 'activate' && user_can('edit_page')) {
        foreach ($ids as $id) {
            if ($page->update_page($id, ['disabled' => 0])) $count++;
        }
        message("$count page(s) activated.", "success");
    } elseif ($bulk_action === 'deactivate' && user_can('edit_page')) {
        foreach ($ids as $id) {
            if ($page->update_page($id, ['disabled' => 1])) $count++;
        }
        message("$count page(s) deactivated.", "success");
    } elseif ($bulk_action === 'delete' && user_can('delete_page')) {
        foreach ($ids as $id) {
            if ($page->delete($id)) $count++;
        }
        message("$count page(s) deleted.", "success");
    } else {
        message("Invalid bulk action or insufficient permissions.", "fail");
    }
} else {
    message("No pages selected or invalid request.", "fail");
}

redirect($admin_route . '/' . $plugin_route);
