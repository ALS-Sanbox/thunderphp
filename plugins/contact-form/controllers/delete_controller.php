<?php

if (user_can('delete_contact_submissions') && csrf_verify($req->post('_token') ?? null) && !empty($id)) {
    $submissions = new \ContactForm\ContactSubmissions;

    if ($submissions->delete($id)) {
        message("Submission deleted.", "success");
    } else {
        message("Failed to delete submission.", "fail");
    }
} else {
    message("Invalid request.", "fail");
}

redirect($admin_route . '/' . $plugin_route);
