<?php
namespace Core;

class View {
    public static function render($template, $data = []) {
        $templatePath = "app/views/" . $template . ".php";
        if (!file_exists($templatePath)) {
            die("View not found: " . $templatePath);
        }
        extract($data);
        ob_start();
        include $templatePath;
        echo ob_get_clean();
    }
}