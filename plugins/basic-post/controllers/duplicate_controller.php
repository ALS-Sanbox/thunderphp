<?php
if (user_can('add_post')) {
    if (csrf_verify($req->post('_token') ?? '')) {
        $source_id = $req->post('source_id');
        $source = $source_id ? $posts->find($source_id) : null;

        if ($source) {
            $title = trim($req->post('title') ?? '') ?: ($source->title . ' (Copy)');
            $slugInput = trim($req->post('slug') ?? '');

            $data = [
                'user_id'      => $user_id,
                'title'        => $title,
                'description'  => $source->description,
                'slug'         => $slugInput ? $posts->makeSlug($slugInput) : $posts->makeSlug($title),
                'keywords'     => $source->keywords,
                'categories'   => $source->categories,
                'content'      => $source->content,
                'pop'          => $source->pop,
                'disabled'     => 1,
                'date_created' => date("Y-m-d H:i:s"),
            ];

            if ($posts->insert($data)) {
                message("Post duplicated successfully!", "success");
            } else {
                message("Failed to duplicate the post." . (!empty($posts->error) ? ' (' . $posts->error . ')' : ''), "fail");
            }
        } else {
            message("Post to duplicate was not found.", "fail");
        }
    } else {
        message("Invalid request.", "fail");
    }

    redirect($admin_route . '/' . $plugin_route);
}
