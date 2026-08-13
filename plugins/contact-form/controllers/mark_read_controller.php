<?php

if (user_can('view_contact_submissions') && csrf_verify($req->post('_token') ?? null) && !empty($id)) {
    $submissions = new \ContactForm\ContactSubmissions;
    $submissions->update($id, ['is_read' => 1]);
}

redirect($admin_route . '/' . $plugin_route . '/' . $id);
