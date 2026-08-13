<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="<?=ROOT?>/assets/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APP_NAME?></title>
</head>
<body>
<?php
$headerLayout = setting('header_layout');
$hasHeaderLayout = is_array($headerLayout) && !empty($headerLayout['html']) && strpos($headerLayout['html'], '{{SITE_MENU}}') !== false;

ob_start();
do_action(plugin_id().'_main_menu', ['links' => $links]);
$menuMarkup = ob_get_clean();

if ($hasHeaderLayout) {
    echo '<style>' . ($headerLayout['css'] ?? '') . '</style>';
    echo str_replace('{{SITE_MENU}}', $menuMarkup, $headerLayout['html']);
} else {
    echo $menuMarkup;
}
?>

