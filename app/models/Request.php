<?php

namespace Core;
defined('ROOT') or die("Direct script access denied");

class Request {
    public $upload_max_size     = 20;
    public $upload_errors       = [];
    public $upload_folder       = 'uploads';
    public $upload_file_types   = [
        'image/jpeg',
        'image/jpg',
        'image/webp',
        'image/gif',
        'image/png',
    ];

    // The destination file extension is always derived from this map, keyed
    // by the *detected* MIME type - never from the client-supplied filename.
    // A file can be renamed to anything (e.g. "shell.php") before upload; only
    // the actual file content decides what extension it's saved with.
    private $mime_extension_map = [
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/png'  => 'png',
    ];

    private function getSuperGlobalValue(string $key = '', string|array $superGlobal = ''): mixed {
        if (empty($key)) {
            return $superGlobal;
        }

        return $superGlobal[$key] ?? '';
    }

    public function method(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function posted(): bool {
        return $this->method() === 'POST';
    }

    public function post(string $key = ''): string|array {
        return $this->getSuperGlobalValue($key, $_POST);
    }

    public function input(string $key, string $default = ''): string {
        return $_POST[$key] ?? $default;
    }

    public function get(string $key = ''): string|array {
        return $this->getSuperGlobalValue($key, $_GET);
    }

    public function files(string $key = ''): string|array {
        return $this->getSuperGlobalValue($key, $_FILES);
    }

    public function all(string $key = ''): string|array {
        return $this->getSuperGlobalValue($key, $_REQUEST);
    }

    public function upload_files(string $key = '', string $directory = ''): string|array {
        $files = $this->files($key);
        if (empty($files) || !isset($files['name']) || empty($files['tmp_name'])) {
            $this->upload_errors[] = "No file uploaded or invalid file.";
            return $this->upload_errors;
        }

        // Ensure the directory exists or create it
        $upload_dir = !empty($directory) ? rtrim($directory, '/') : $this->upload_folder;
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                $this->upload_errors[] = "Failed to create upload directory.";
                return $this->upload_errors;
            }
        }

        // Strip anything but alphanumerics/dash/underscore from the client's
        // filename - it's only ever used for the human-readable part of the
        // saved name, so this is defense-in-depth, not the primary fix below.
        $file_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($files['name'], PATHINFO_FILENAME));
        if ($file_name === '') {
            $file_name = 'file';
        }
        $file_tmp  = $files['tmp_name'];

        if (!file_exists($file_tmp) || empty($file_tmp)) {
            $this->upload_errors[] = "Temporary file is missing.";
            return $this->upload_errors;
        }

        $file_size = $files['size'] / (1024 * 1024); // Convert size to MB
        $file_type = mime_content_type($file_tmp);

        // Validate file type
        if (!in_array($file_type, $this->upload_file_types) || !isset($this->mime_extension_map[$file_type])) {
            $this->upload_errors[] = "Invalid file type: " . $file_type;
            return $this->upload_errors;
        }

        // The extension is derived solely from the detected MIME type above -
        // never from the client-supplied filename - so a file can't be saved
        // with an executable extension (e.g. .php) regardless of what it was
        // named before upload.
        $file_extension = $this->mime_extension_map[$file_type];

        // Validate file size
        if ($file_size > $this->upload_max_size) {
            $this->upload_errors[] = "File size exceeds limit of " . $this->upload_max_size . "MB.";
            return $this->upload_errors;
        }

        // Create unique file name
        $upload_path = $upload_dir . '/' . $file_name . '.' . $file_extension;
        $counter = 1;
        while (file_exists($upload_path)) {
            $upload_path = $upload_dir . '/' . $file_name . "_" . $counter . '.' . $file_extension;
            $counter++;
        }

        // Try to move the file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Log info for debugging
            error_log("File uploaded to: $upload_path");
            error_log("File type: $file_type");

            return $upload_path;
        } else {
            $this->upload_errors[] = "Failed to move uploaded file.";
            return $this->upload_errors;
        }
    }

    public function upload_multiple_files(string $key = '', string $directory = ''): array {
        $files = $this->files($key);
        $uploaded_files = [];

        if (empty($files) || !isset($files['name']) || !is_array($files['name'])) {
            $this->upload_errors[] = "No files uploaded.";
            return $this->upload_errors;
        }

        foreach ($files['name'] as $index => $file_name) {
            $file_data = [
                'name'     => $file_name,
                'tmp_name' => $files['tmp_name'][$index],
                'size'     => $files['size'][$index],
                'type'     => $files['type'][$index]
            ];

            $_FILES[$key] = $file_data;
            $result = $this->upload_files($key, $directory);

            if (is_array($result)) {
                $uploaded_files = array_merge($uploaded_files, $result);
            } else {
                $uploaded_files[] = $result;
            }
        }

        return $uploaded_files;
    }

    public function delete_file(string $file_path): bool {
        return file_exists($file_path) ? unlink($file_path) : false;
    }
}
