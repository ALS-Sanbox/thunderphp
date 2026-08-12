<?php
if (user_can('add_page')) {
    if (csrf_verify($req->post('_token') ?? '')) {
        $source_id = $req->post('source_id');
        $source = $source_id ? $page->find($source_id) : null;

        if ($source) {
            $title = trim($req->post('title') ?? '') ?: ($source->title . ' (Copy)');
            $slugInput = trim($req->post('slug') ?? '');

            $data = [
                'user_id'         => $user_id,
                'title'           => $title,
                'description'     => $source->description,
                'slug'            => $slugInput ? $page->makeSlug($slugInput) : $page->makeSlug($title),
                'keywords'        => $source->keywords,
                'categories'      => $source->categories,
                'content'         => $source->content,
                'advancedcontent' => $source->advancedcontent,
                'advanced'        => $source->advanced,
                'disabled'        => 1,
                'date_created'    => date("Y-m-d H:i:s"),
            ];

            if ($page->insert($data)) {
                message("Page duplicated successfully!", "success");
            } else {
                message("Failed to duplicate the page." . (!empty($page->error) ? ' (' . $page->error . ')' : ''), "fail");
            }
        } else {
            message("Page to duplicate was not found.", "fail");
        }
    } else {
        message("Invalid request.", "fail");
    }

    redirect($admin_route . '/' . $plugin_route);
}
