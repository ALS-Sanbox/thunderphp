<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="<?=ROOT?>/assets/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc(do_filter('page_title', APP_NAME)) ?></title>
    <?php do_action('before_head_close'); ?>
</head>
<body>
<?php
$headerLayout = setting('header_layout');
$headerTokens = ['{{SITE_MENU}}', '{{SITE_LOGO}}', '{{HOME_LINK}}', '{{USER_MENU}}'];
$hasHeaderLayout = is_array($headerLayout) && !empty($headerLayout['html']) && array_reduce(
    $headerTokens,
    fn($found, $token) => $found || strpos($headerLayout['html'], $token) !== false,
    false
);

ob_start();
do_action(plugin_id().'_main_menu', ['links' => $links]);
$menuMarkup = ob_get_clean();

ob_start();
do_action('header-footer_user_menu');
$userMenuMarkup = ob_get_clean();

$logoMarkup = '<a href="' . esc(ROOT) . '"><img src="' . esc(get_image(setting('site_logo'))) . '" alt="' . esc(APP_NAME) . '" style="max-height:48px;"></a>';
$homeLinkMarkup = '<a href="' . esc(ROOT) . '">Home</a>';

if ($hasHeaderLayout) {
    $merged = str_replace(
        $headerTokens,
        [$menuMarkup, $logoMarkup, $homeLinkMarkup, $userMenuMarkup],
        $headerLayout['html']
    );

    echo '<style>' . ($headerLayout['css'] ?? '') . '</style>';
    echo $merged;
} else {
    echo $menuMarkup;
    echo $userMenuMarkup;
}
?>

