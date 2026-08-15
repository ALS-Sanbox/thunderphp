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
    // No custom header layout saved yet (true for every fresh install,
    // until an admin builds one in the header editor) - $menuMarkup is a
    // complete, self-contained <nav> from site-menus with nowhere for the
    // account area to plug into, so without this it just falls to a new
    // line below the nav, left-aligned. Pins it to the nav's own top-right
    // corner instead, matching site-menus' nav height (50px) and dark
    // background (#242526) - both hardcoded here since this fallback only
    // has to look right against site-menus' own default nav, not any
    // possible custom one. Below the 970px breakpoint where that nav
    // collapses to a hamburger menu, drops back to static/stacked instead
    // of risking an overlap with the menu button.
    //
    // site-menus' own nav also needs one nudge here: its .wrapper uses
    // justify-content:space-between against a dead, non-functional
    // ("href=#") "Logo" placeholder, which pushes the real menu links
    // (Home, by default) to the *right* edge of the bar - competing with
    // the slot below for the same corner instead of sitting on the left
    // the way a primary nav normally does. Hiding that placeholder here
    // leaves nav-links as .wrapper's only flex child, which - under
    // space-between with a single item - lands at flex-start (left) on
    // its own, no further override needed.
    ?>
    <div style="position:relative;">
        <?= $menuMarkup ?>
        <div class="hf-default-user-menu-slot"><?= $userMenuMarkup ?></div>
    </div>
    <style>
        .wrapper .logo {
            display: none;
        }
        .hf-default-user-menu-slot {
            position: absolute;
            top: 0;
            right: 20px;
            height: 50px;
            display: flex;
            align-items: center;
            color: #f2f2f2;
        }
        .hf-default-user-menu-slot a,
        .hf-default-user-menu-slot span {
            color: #f2f2f2;
        }
        @media screen and (max-width: 970px) {
            .hf-default-user-menu-slot {
                position: static;
                height: auto;
                padding: 10px 20px;
                color: initial;
            }
            .hf-default-user-menu-slot a,
            .hf-default-user-menu-slot span {
                color: initial;
            }
        }
    </style>
    <?php
}
?>

