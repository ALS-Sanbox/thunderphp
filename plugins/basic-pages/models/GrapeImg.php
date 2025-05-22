<?php
namespace BasicPages;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class GrapeImg extends Model
{
    public function upload()
    {
        $uploadDir = plugin_path('uploads/');
        $uploadUrl = plugin_http_path('uploads/');
        $response = ['data' => []];
dd($response);
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!empty($_FILES['files'])) {
            foreach ($_FILES['files']['name'] as $index => $name) {
                $tmpName = $_FILES['files']['tmp_name'][$index];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array(strtolower($ext), $allowed)) {
                    continue;
                }

                $filename = uniqid('img_', true) . '.' . $ext;
                $targetPath = $uploadDir . $filename;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $response['data'][] = ['src' => $uploadUrl . $filename];
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}