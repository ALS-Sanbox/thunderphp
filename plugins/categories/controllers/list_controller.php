<?php
// categories_list_controller.php
message('');

if (!isset($categories)) {
    die("Error: Categories object not initialized!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $postdata = $req->post();
    $action = $_POST['action'];
    $categoryId = $postdata['id'] = $_POST['category_id'] ?? null;
    $categoryName = $postdata['category'] = $_POST['category'] ?? null;
    $slug = $postdata['slug'] = $_POST['slug'] ?? null;
    $activeStatus = $postdata['disabled'] = $_POST['active'] ?? 0;
    $csrf = csrf_verify($_POST['_token']);

    switch ($action) {
        case 'add':
            if (!empty($categoryName)) {
                if ($csrf && $categories->validate_insert($postdata)) {
                    if (user_can('add_category')) {
                        $categories->insert($postdata);
                        message('Category "' . $categoryName . '" added successfully!', 'success');
                    }
                } else {
                    set_value('errors', $categories->errors);
                    message(get_value('errors')['category'] ?? 'Error adding category.');
                }
            } else {
                message("Category name cannot be empty.");
            }
            break;

        case 'update':
            if (!empty($categoryId)) {
                if ($csrf && $categories->validate_update($postdata)) {
                    if (user_can('edit_category')) {
                        $categories->update($categoryId, $postdata);
                        message('Category updated successfully.', 'success');
                    }
                } else {
                    set_value('errors', $categories->errors);
                    message(get_value('errors')['category'] ?? 'Error updating category.');
                }
            } else {
                message("Category ID is required.");
            }
            break;

        case 'delete':
            if ($csrf) {
                if (user_can('delete_category')) {
                    $categories->delete($categoryId);
                    message('Category deleted successfully.', 'success');
                } else {
                    message("You don't have permission to delete categories.");
                }
            } else {
                message("Form expired.");
            }
            break;

        default:
            message("Invalid action.");
            break;
    }
}