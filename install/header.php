<?php
defined('INSTALL_ROOT') or die('Direct access not permitted');

$installStepOrder = ['profile', 'requirements', 'database', 'installing', 'site', 'done'];
$installStepLabels = [
    'profile'      => 'Profile',
    'requirements' => 'Requirements',
    'database'     => 'Database',
    'installing'   => 'Install',
    'site'         => 'Site',
    'done'         => 'Done',
];
$currentIndex = array_search($step ?? 'profile', $installStepOrder, true) ?: 0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install ThunderPHP</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body { background: #f4f5f7; }
        .install-wrap { max-width: 680px; margin: 48px auto; }
        .install-steps { display: flex; justify-content: space-between; margin-bottom: 28px; }
        .install-steps .install-step { flex: 1; text-align: center; font-size: 0.8rem; color: #adb5bd; position: relative; padding-bottom: 8px; border-bottom: 3px solid #dee2e6; }
        .install-steps .install-step.is-done { color: #198754; border-bottom-color: #198754; }
        .install-steps .install-step.is-current { color: #0d6efd; font-weight: 600; border-bottom-color: #0d6efd; }
        .install-log { background: #111827; color: #d1d5db; font-family: monospace; font-size: 0.85rem; padding: 16px; border-radius: 6px; max-height: 320px; overflow-y: auto; white-space: pre-wrap; }
    </style>
</head>
<body>
<div class="install-wrap">
    <h1 class="h4 mb-4 text-center">ThunderPHP Installation</h1>

    <div class="install-steps">
        <?php foreach ($installStepOrder as $i => $key): ?>
            <div class="install-step <?= $i < $currentIndex ? 'is-done' : ($i === $currentIndex ? 'is-current' : '') ?>">
                <?= install_esc($installStepLabels[$key]) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
