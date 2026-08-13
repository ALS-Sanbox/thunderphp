<?php

if (!user_can('manage_seo')) {
    return;
}

$req = new \Core\Request;
$postdata = $req->post();

if (!csrf_verify($postdata['_token'] ?? null)) {
    message("Form expired! Please refresh the page.", "fail");
    redirect($admin_route . '/' . $plugin_route);
}

$db = new \Core\Database;

if (($postdata['action'] ?? '') === 'regenerate') {
    seo_build_robots();
    seo_build_sitemap($db);
    message("Sitemap and robots.txt regenerated.", "success");
    redirect($admin_route . '/' . $plugin_route);
}

$filedata = $req->files();
if (!empty($filedata['og_image']) && $filedata['og_image']['error'] != UPLOAD_ERR_NO_FILE) {
    $result = $req->upload_files('og_image');

    if (is_array($result)) {
        message(implode(' ', $req->upload_errors), 'fail');
        redirect($admin_route . '/' . $plugin_route);
    }

    seo_set_setting($db, 'seo_default_og_image', $result);
}

seo_set_setting($db, 'seo_default_description', trim((string) ($postdata['seo_default_description'] ?? '')));
seo_set_setting($db, 'seo_default_keywords', trim((string) ($postdata['seo_default_keywords'] ?? '')));
seo_set_setting($db, 'seo_robots_txt', trim((string) ($postdata['seo_robots_txt'] ?? '')));
seo_set_setting($db, 'seo_include_pages', !empty($postdata['seo_include_pages']) ? '1' : '0');
seo_set_setting($db, 'seo_include_posts', !empty($postdata['seo_include_posts']) ? '1' : '0');

seo_build_robots();
seo_build_sitemap($db);

message("SEO settings saved.", "success");
redirect($admin_route . '/' . $plugin_route);
