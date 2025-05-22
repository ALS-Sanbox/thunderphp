<?php
if (user_can('delete_page')) {
    $record_id = $req->post('page_id');

    if (csrf_verify($req->post('_token')) && !empty($record_id)) {

        // Load content before deleting
        $pageData = $page->first(['id' => $record_id]);
        if ($pageData && isset($pageData->content)) {
            // Delete images in content
            $contentModel = new \BasicPages\Content();
            $contentModel->delete_images($pageData->content);
        }

        // Delete the page record
        if ($page->delete($record_id)) {
            message("Record deleted successfully!", "success");
            redirect($admin_route . '/' . $plugin_route . '/list');
        } else {
            message("Failed to delete record.", "fail");
        }
    } else {
        message("Invalid request.", "fail");
    }
}